<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Request\TaskUpdateRequest;
use App\Module\Board\Application\Http\API\V1\Request\CommentCreateRequest;
use App\Module\Board\Application\UseCase\TaskCreate\TaskCreateCommand;
use App\Module\Board\Application\UseCase\TaskUpdate\TaskUpdateCommand;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Bus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    public function __construct(
        private readonly BoardRepository $boardRepository,
        private readonly TaskRepository $taskRepository,
        private readonly CommandBus $commandBus
    ) {}

    #[Route(
        '/api/v1/task/',
        methods: ['POST']
    )]
    public function create(CommentCreateRequest $request): Response
    {
        $board = $this->boardRepository->findById($request->getTaskId());

        if ($board === null) {
            return new Response('', 404);
        }

        $this->commandBus->execute(new TaskCreateCommand($request->getTaskId(), $request->getContent()));

        return $this->json([]);
    }

    #[Route(
        '/api/v1/task/{id}',
        methods: ['PUT']
    )]
    public function update(TaskUpdateRequest $request): Response
    {
        $task = $this->taskRepository->findById($request->getId());

        if ($task === null) {
            return new Response('', 404);
        }

        $this->commandBus->execute(
            new TaskUpdateCommand($request->getId(), $request->getTitle(), $request->getState())
        );

        return $this->json([]);
    }
}