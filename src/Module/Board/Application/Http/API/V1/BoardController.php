<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Request\BoardUpdateRequest;
use App\Module\Board\Application\UseCase\BoardTitleUpdate\BoardTitleUpdateCommand;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Repository\CommentRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Bus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoardController extends AbstractController
{
    public function __construct(
        private readonly BoardRepository $boardRepository,
        private readonly TaskRepository $taskRepository,
        private readonly CommentRepository $commentRepository,
        private readonly CommandBus $commandBus
    ) {}

    #[Route(
        '/api/v1/board/{id}',
        methods: ['GET']
    )]
    public function board(string $id): Response
    {
        $board = $this->boardRepository->findById($id);

        if ($board === null) {
            return new Response('', 404);
        }

        $tasks = [];
        $taskIds = [];

        foreach ($this->taskRepository->findByBoard($board) as $task) {
            $taskIds[] = $task->getId()->toString();

            $tasks[$task->getId()->toString()] = [
                'id' => $task->getId()->toString(),
                'title' => $task->getTitle(),
                'state' => $task->getState(),
                'comments' => []
            ];
        }

        if (!empty($taskIds)) {
            foreach ($this->commentRepository->findByTaskIds($taskIds) as $comment) {
                $tasks[$comment->getTask()->getId()->toString()]['comments'][] = [
                    'id' => $comment->getId()->toString(),
                    'context' => $comment->getContent(),
                ];
            }
        }

        return $this->json([
            'title' => $board->getTitle(),
            'tasks' => $tasks
        ]);
    }

    #[Route(
        '/api/v1/board/{id}',
        methods: ['PUT']
    )]
    public function update(string $id, BoardUpdateRequest $request): Response
    {
        $board = $this->boardRepository->findById($id);

        if ($board === null) {
            return new Response('', 404);
        }

        $this->commandBus->execute(new BoardTitleUpdateCommand($id, $request->getTitle()));

        return $this->json([]);
    }
}