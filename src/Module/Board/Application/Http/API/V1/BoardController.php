<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Model\Board as BoardModel;
use App\Module\Board\Application\Http\API\V1\Request\BoardPinCodeRequest;
use App\Module\Board\Application\Http\API\V1\Request\BoardPatchRequest;
use App\Module\Board\Application\Http\API\V1\Response\BoardCreateResponse;
use App\Module\Board\Application\Service\BoardAccessManagerInterface;
use App\Module\Board\Application\Service\BoardsCookieJar;
use App\Module\Board\Application\UseCase\BoardCreate\BoardCreateCommand;
use App\Module\Board\Application\UseCase\BoardDelete\BoardDeleteCommand;
use App\Module\Board\Application\UseCase\BoardHistoryUpdate\BoardHistoryUpdateCommand;
use App\Module\Board\Application\UseCase\BoardRemovePinCode\BoardRemovePinCodeCommand;
use App\Module\Board\Application\UseCase\BoardSetPinCode\BoardSetPinCodeCommand;
use App\Module\Board\Application\UseCase\BoardTakeOwnership\BoardTakeOwnershipCommand;
use App\Module\Board\Application\UseCase\BoardTitleUpdate\BoardUpdateCommand;
use App\Module\Board\Domain\Entity\BoardId;
use App\Module\Board\Domain\Entity\BoardVisitedHistory;
use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Board\Domain\Repository\BoardVisitedHistoryRepository;
use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Board\Domain\Repository\TaskLabelRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Board\Infrastructure\Service\PinCodeHasher;
use App\Module\Common\Bus\CommandBus;
use App\Module\Core\Domain\Entity\User;
use App\Module\Core\Domain\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BoardController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly CommentRepository $commentRepository,
        private readonly TaskLabelRepository $taskLabelRepository,
        private readonly CommandBus $commandBus,
        private readonly BoardIdRepository $boardIdRepository,
        private readonly BoardsCookieJar $boardsCookieJar,
        private readonly UserRepository $userRepository,
        private readonly BoardAccessManagerInterface $boardAccessManager,
        private readonly BoardVisitedHistoryRepository $boardVisitedHistoryRepository,
        private readonly PinCodeHasher $pinCodeHasher
    ) {}

    /**
     * Create a new board
     */
    #[Route(
        '/api/v1/board',
        methods: ['POST']
    )]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: 'Returns ID of the new board',
        content: new Model(type: BoardCreateResponse::class)
    )]
    public function create(): Response
    {
        $command = new BoardCreateCommand();

        $this->commandBus->execute($command);

        return $this->json([
            'id' => $command->getId(),
        ]);
    }

    /**
     * Get info about board
     */
    #[Route(
        '/api/v1/board/{id}',
        methods: ['GET']
    )]
    #[OA\Header(
        header: 'x-board-pin-code',
        description: 'Pin code',
        schema: new OA\Schema(type: 'string', example: '123445')
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID of the board',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', example: '839cf68e-4062-4259-addc-09ce5644ee52')
    )]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about board',
        content: new Model(type: BoardModel::class)
    )]
    #[IsGranted('view', 'boardId')]
    public function board(BoardId $boardId): Response
    {
        /** @var ?User $user */
        $user = $this->getUser();

        if ($user !== null) {
            $this->commandBus->execute(new BoardHistoryUpdateCommand($boardId->getId()->toString(), $user));
        }

        return $this->json($this->getFormattedBoard($boardId));
    }

    /**
     * Update board
     */
    #[Route(
        '/api/v1/board/{id}',
        methods: ['PUT']
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID of the board',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', example: '839cf68e-4062-4259-addc-09ce5644ee52')
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: BoardPatchRequest::class)))]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about board',
        content: new Model(type: BoardModel::class)
    )]
    #[IsGranted('edit', 'boardId')]
    public function update(BoardId $boardId, BoardPatchRequest $request): Response
    {
        $this->commandBus->execute(
            new BoardUpdateCommand(
                $boardId->getBoard()->getId()->toString(),
                $request->getTitle(),
                $request->getDisplay(),
                $request->getThemeColor()
            )
        );

        return $this->json($this->getFormattedBoard($boardId));
    }

    private function getFormattedBoard(BoardId $boardId): array
    {
        $tasks = [];
        $archivedTasks = [];
        $taskIds = [];
        $board = $boardId->getBoard();

        foreach ($this->taskLabelRepository->findByBoard($board) as $taskLabel) {
            $label = $taskLabel->getLabel();

            $labels[$taskLabel->getTask()->getId()->toString()][] = [
                'id' => $taskLabel->getId()->toString(),
                'taskId' => $taskLabel->getTask()->getId()->toString(),
                'label' => [
                    'id' => $label->getId()->toString(),
                    'title' => $label->getTitle(),
                    'color' => $label->getColor(),
                ]
            ];
        }

        foreach ($this->taskRepository->findActiveByBoard($board) as $task) {
            $taskIds[] = $task->getId()->toString();

            $tasks[$task->getId()->toString()] = [
                'id' => $task->getId()->toString(),
                'title' => $task->getTitle(),
                'status' => $task->getState(),
                'labels' => $labels[$task->getId()->toString()] ?? [],
                'comments' => []
            ];
        }

        foreach ($this->taskRepository->findArchivedByBoard($board) as $task) {
            $taskIds[] = $task->getId()->toString();

            $archivedTasks[$task->getId()->toString()] = [
                'id' => $task->getId()->toString(),
                'title' => $task->getTitle(),
                'status' => $task->getState(),
                'labels' => $labels[$task->getId()->toString()] ?? [],
                'comments' => []
            ];
        }

        if (!empty($taskIds)) {
            foreach ($this->commentRepository->findByTaskIds($taskIds) as $comment) {
                if (isset($tasks[$comment->getTask()->getId()->toString()])) {
                    $tasks[$comment->getTask()->getId()->toString()]['comments'][] = [
                        'id' => $comment->getId()->toString(),
                        'content' => $comment->getContent(),
                    ];
                }

                if (isset($archivedTasks[$comment->getTask()->getId()->toString()])) {
                    $archivedTasks[$comment->getTask()->getId()->toString()]['comments'][] = [
                        'id' => $comment->getId()->toString(),
                        'content' => $comment->getContent(),
                    ];
                }
            }
        }

        return [
            'id' => $boardId->getId(),
            'readOnlyUrl' => $boardId->isReadOnly()
                ? null
                : $this->generateUrl(
                    'board.index',
                    ['id' => $this->boardIdRepository->getReadonlyByBoard($board)->getId()->toString()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            'title' => $board->getTitle() === null ? '' : $boardId->getBoard()->getTitle(),
            'display' => $board->getDisplay(),
            'themeColor' => $board->getThemeColor(),
            'tasks' => array_values($tasks),
            'archivedTasks' => array_values($archivedTasks),
            'readOnly' => $boardId->isReadOnly(),
            'ownership' => $board->hasOwner(),
            'isOwner' => $board->isOwner($this->getUser()),
            'hasPinCode' => $boardId->hasPinCode()
        ];
    }

    /**
     * Get info about board
     */
    #[Route(
        '/api/v1/boards/',
        methods: ['GET']
    )]
    #[OA\Tag(name: 'Boards')]
    public function boards(): Response
    {
        $isAuthenticated = $this->getUser() !== null;

        if ($isAuthenticated) {
            $visited = array_map(
                fn (BoardVisitedHistory $boardVisitedHistory) => [
                    'id' => $boardVisitedHistory->getBoardId()->getId()->toString(),
                    'title' => $boardVisitedHistory->getBoardId()->getBoard()->getTitle(),
                    'readOnly' => $boardVisitedHistory->getBoardId()->isReadOnly(),
                    'hasPinCode' => $boardVisitedHistory->getBoardId()->hasPinCode(),
                    'themeColor' => $boardVisitedHistory->getBoardId()->getBoard()->getThemeColor(),
                ],
                $this->boardVisitedHistoryRepository->findByOwner($this->getUser()->getUserIdentifier())
            );
        } else {
            $boardsIds = $this->boardsCookieJar->all();
            $visited = [];

            if (!empty($boardsIds)) {
                foreach ($this->boardIdRepository->findByIds($boardsIds) as $boardId) {
                    $visited[] = [
                        'id' => $boardId->getId()->toString(),
                        'title' => $boardId->getBoard()->getTitle(),
                        'readOnly' => $boardId->isReadOnly(),
                        'hasPinCode' => $boardId->hasPinCode(),
                        'themeColor' => $boardId->getBoard()->getThemeColor(),
                    ];
                }
            }
        }

        return $this->json([
            'my' => $this->getUser() === null
                ? []
                : array_map(
                    fn (BoardId $boardId) => [
                        'id' => $boardId->getId()->toString(),
                        'title' => $boardId->getBoard()->getTitle(),
                        'readOnly' => $boardId->isReadOnly(),
                        'hasPinCode' => $boardId->hasPinCode(),
                        'themeColor' => $boardId->getBoard()->getThemeColor(),
                    ],
                    $this->boardIdRepository->findByOwner($this->getUser()->getUserIdentifier())
                ),
            'visited' => $visited
        ]);
    }

    #[Route(
        '/api/v1/board/{id}',
        methods: ['DELETE']
    )]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: '',
    )]
    #[IsGranted('edit', 'boardId')]
    public function delete(BoardId $boardId): Response
    {
        $this->commandBus->execute(
            new BoardDeleteCommand($boardId->getId()->toString())
        );

        return $this->json([]);
    }

    #[Route(
        '/api/v1/board/{id}/take-ownership',
        methods: ['POST']
    )]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: '',
    )]
    #[IsGranted('edit', 'boardId')]
    public function takeOwnership(BoardId $boardId): Response
    {
        if ($boardId->getBoard()->hasOwner()) {
            return new Response('Already has owner', Response::HTTP_FORBIDDEN);
        }

        $this->commandBus->execute(
            new BoardTakeOwnershipCommand(
                $boardId->getBoard()->getId()->toString(),
                $this->userRepository->getByEmail($this->getUser()->getUserIdentifier())->getId()->toString(),
            )
        );

        return $this->json([]);
    }

    #[Route(
        '/api/v1/board/{id}/pin-code',
        methods: ['POST']
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: BoardPinCodeRequest::class)))]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: '',
    )]
    public function setPinCode(BoardId $boardId, BoardPinCodeRequest $request): Response
    {
        if (!$boardId->getBoard()->isOwner($this->getUser())) {
            return new Response('No access to to set pin code the the the board', Response::HTTP_FORBIDDEN);
        }

        $this->commandBus->execute(
            new BoardSetPinCodeCommand(
                $boardId->getId()->toString(),
                $request->getPinCode()
            )
        );

        return $this->json([]);
    }

    #[Route(
        '/api/v1/board/{id}/pin-code',
        methods: ['DELETE']
    )]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: '',
    )]
    public function removePinCode(BoardId $boardId): Response
    {
        if (!$boardId->getBoard()->isOwner($this->getUser())) {
            return new Response('No access to to set pin code the the the board', Response::HTTP_FORBIDDEN);
        }

        $this->commandBus->execute(
            new BoardRemovePinCodeCommand($boardId->getId()->toString())
        );

        return $this->json([]);
    }

    #[Route(
        '/api/v1/board/{id}/access/{pinCode}',
        methods: ['GET']
    )]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: '',
    )]
    public function checkAccess(BoardId $boardId, string $pinCode): Response
    {
        if ($boardId->getPinCode() === null) {
            return $this->json(['access' => true]);
        }

        return $this->json([
            'access' => $this->pinCodeHasher->getHasher()->verify(
                $boardId->getPinCode(),
                $pinCode
            )
        ]);
    }

    /**
     * Update board params
     */
    #[Route(
        '/api/v1/board/{id}',
        methods: ['PATCH']
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID of the board',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', example: '839cf68e-4062-4259-addc-09ce5644ee52')
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: BoardPatchRequest::class)))]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about board',
        content: new Model(type: BoardModel::class)
    )]
    #[IsGranted('edit', 'boardId')]
    public function patch(BoardId $boardId, BoardPatchRequest $request): Response
    {
        $this->commandBus->execute(
            new BoardUpdateCommand(
                $boardId->getBoard()->getId()->toString(),
                $request->getTitle(),
                $request->getDisplay(),
                $request->getThemeColor()
            )
        );

        return $this->json($this->getFormattedBoard($boardId));
    }
}
