<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\DTO\Board as BoardDTO;
use App\Module\Board\Application\Http\API\V1\Model\Board as BoardModel;
use App\Module\Board\Application\Http\API\V1\Request\BoardUpdateRequest;
use App\Module\Board\Application\Http\API\V1\Response\BoardCreateResponse;
use App\Module\Board\Application\Service\BoardFinder;
use App\Module\Board\Application\Service\BoardsCookieJar;
use App\Module\Board\Application\UseCase\BoardCreate\BoardCreateCommand;
use App\Module\Board\Application\UseCase\BoardDelete\BoardDeleteCommand;
use App\Module\Board\Application\UseCase\BoardTakeOwnership\BoardTakeOwnershipCommand;
use App\Module\Board\Application\UseCase\BoardTitleUpdate\BoardUpdateCommand;
use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Board\Domain\Repository\TaskLabelRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Board\Domain\Service\ReadOnlyBoardKeeper;
use App\Module\Common\Bus\CommandBus;
use App\Module\Core\Domain\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BoardController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository      $taskRepository,
        private readonly CommentRepository   $commentRepository,
        private readonly TaskLabelRepository $taskLabelRepository,
        private readonly CommandBus          $commandBus,
        private readonly BoardFinder         $boardFinder,
        private readonly ReadOnlyBoardKeeper $boardKeeper,
        private readonly BoardsCookieJar $boardsCookieJar,
        private readonly BoardRepository $boardRepository,
        private readonly UserRepository $userRepository
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
    public function board(string $id): Response
    {
        $board = $this->boardFinder->findById($id);

        if ($board === null) {
            return new Response('', 404);
        }

        if ($board->isReadOnly()) {
            $this->boardKeeper->addBoard($board->getBoard());
        } else {
            $this->boardKeeper->removeBoard($board->getBoard());
        }

        return $this->json($this->getFormattedBoard($board));
    }

    /**
     * Update board title
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
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: BoardUpdateRequest::class)))]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about board',
        content: new Model(type: BoardModel::class)
    )]
    public function update(string $id, BoardUpdateRequest $request): Response
    {
        $board = $this->boardFinder->findById($id);

        if ($board === null) {
            return new Response('', 404);
        }

        if ($this->boardKeeper->exists($board->getBoard())) {
            return new Response('No access to update board', 403);
        }

        $this->commandBus->execute(
            new BoardUpdateCommand(
                $board->getBoard()->getId()->toString(),
                $request->getTitle(),
                $request->getDisplay()
            )
        );

        return $this->json($this->getFormattedBoard($board));
    }

    private function getFormattedBoard(BoardDTO $boardDTO): array
    {
        $tasks = [];
        $archivedTasks = [];
        $taskIds = [];

        foreach ($this->taskLabelRepository->findByBoard($boardDTO->getBoard()) as $taskLabel) {
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

        foreach ($this->taskRepository->findActiveByBoard($boardDTO->getBoard()) as $task) {
            $taskIds[] = $task->getId()->toString();

            $tasks[$task->getId()->toString()] = [
                'id' => $task->getId()->toString(),
                'title' => $task->getTitle(),
                'status' => $task->getState(),
                'labels' => $labels[$task->getId()->toString()] ?? [],
                'comments' => []
            ];
        }

        foreach ($this->taskRepository->findArchivedByBoard($boardDTO->getBoard()) as $task) {
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
            'id' => $boardDTO->isReadOnly() ? $boardDTO->getBoard()->getReadOnlyId() : $boardDTO->getBoard()->getId(),
            'readOnlyUrl' => $boardDTO->isReadOnly()
                ? null
                : $this->generateUrl(
                    'board.index',
                    ['id' => $boardDTO->getBoard()->getReadOnlyId()->toString()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            'title' => $boardDTO->getBoard()->getTitle() === null ? '' : $boardDTO->getBoard()->getTitle(),
            'display' => $boardDTO->getBoard()->getDisplay(),
            'tasks' => array_values($tasks),
            'archivedTasks' => array_values($archivedTasks),
            'readOnly' => $boardDTO->isReadOnly(),
            'ownership' => $boardDTO->getBoard()->hasOwner()
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
        $boardsIds = $this->boardsCookieJar->all();

        if (empty($boardsIds)) {
            return new Response('', 404);
        }

        $result = [];

        foreach ($this->boardRepository->findByIds($boardsIds) as $board) {
            if (in_array($board->getReadOnlyId()->toString(), $boardsIds)) {
                $result[] = [
                    'id' => $board->getReadOnlyId()->toString(),
                    'title' => $board->getTitle(),
                    'readOnly' => true
                ];
            }

            if (in_array($board->getId()->toString(), $boardsIds)) {
                $result[] = ['id' => $board->getId()->toString(), 'title' => $board->getTitle(), 'readOnly' => false];
            }
        }

        return $this->json([
            'my' => $this->getUser() === null
                ? []
                : array_map(
                    fn (Board $board) => [
                        'id' => $board->getId()->toString(),
                        'title' => $board->getTitle(),
                        'readOnly' => false
                    ],
                    $this->boardRepository->findByOwner($this->getUser()->getUserIdentifier())
                ),
            'visited' => $result
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
    public function delete(string $id): Response
    {
        $board = $this->boardFinder->findById($id);

        if ($board === null) {
            return new Response('', 404);
        }

        if ($this->boardKeeper->exists($board->getBoard())) {
            return new Response('No access to delete the board', 403);
        }

        $this->commandBus->execute(
            new BoardDeleteCommand($id)
        );

        return $this->json([]);
    }

    #[Route(
        '/api/v1/board/take-ownership/{id}',
        methods: ['POST']
    )]
    #[OA\Tag(name: 'Board')]
    #[OA\Response(
        response: 200,
        description: '',
    )]
    public function takeOwnership(string $id): Response
    {
        $board = $this->boardFinder->findById($id);

        if ($board === null) {
            return new Response('', 404);
        }

        if ($this->boardKeeper->exists($board->getBoard())) {
            return new Response('No access to delete the board', 403);
        }

        if ($board->getBoard()->hasOwner()) {
            return new Response('Already has owner', 403);
        }

        $this->commandBus->execute(
            new BoardTakeOwnershipCommand(
                $board->getBoard()->getId()->toString(),
                $this->userRepository->getByEmail($this->getUser()->getUserIdentifier())->getId()->toString(),
            )
        );

        return $this->json([]);
    }
}
