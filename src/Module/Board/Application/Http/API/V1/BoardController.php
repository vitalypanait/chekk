<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Model\Board as BoardModel;
use App\Module\Board\Application\Http\API\V1\Request\BoardUpdateRequest;
use App\Module\Board\Application\Http\API\V1\Response\BoardCreateResponse;
use App\Module\Board\Application\UseCase\BoardCreate\BoardCreateCommand;
use App\Module\Board\Application\UseCase\BoardTitleUpdate\BoardTitleUpdateCommand;
use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Board\Domain\Repository\TaskLabelRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Bus\CommandBus;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoardController extends AbstractController
{
    public function __construct(
        private readonly BoardRepository     $boardRepository,
        private readonly TaskRepository      $taskRepository,
        private readonly CommentRepository   $commentRepository,
        private readonly TaskLabelRepository $taskLabelRepository,
        private readonly CommandBus          $commandBus
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
        $board = $this->boardRepository->findById($id);

        if ($board === null) {
            return new Response('', 404);
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
        $board = $this->boardRepository->findById($id);

        if ($board === null) {
            return new Response('', 404);
        }

        $this->commandBus->execute(new BoardTitleUpdateCommand($id, $request->getTitle(), $request->getType()));

        return $this->json($this->getFormattedBoard($board));
    }

    private function getFormattedBoard(Board $board): array
    {
        $tasks = [];
        $archivedTasks = [];
        $taskIds = [];

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
            'id' => $board->getId(),
            'title' => $board->getTitle() === null ? '' : $board->getTitle(),
            'type' => $board->getType(),
            'tasks' => array_values($tasks),
            'archivedTasks' => array_values($archivedTasks)
        ];
    }
}
