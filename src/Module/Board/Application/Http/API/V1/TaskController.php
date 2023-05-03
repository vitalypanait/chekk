<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Model\Comment;
use App\Module\Board\Application\Http\API\V1\Model\Task;
use App\Module\Board\Application\Http\API\V1\Request\TaskCreateRequest;
use App\Module\Board\Application\Http\API\V1\Request\TaskUpdateRequest;
use App\Module\Board\Application\UseCase\TaskCreate\TaskCreateCommand;
use App\Module\Board\Application\UseCase\TaskDelete\TaskDeleteCommand;
use App\Module\Board\Application\UseCase\TaskUpdate\TaskUpdateCommand;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Bus\CommandBus;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
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
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: TaskCreateRequest::class)))]
    #[OA\Tag(name: 'Task')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about task',
        content: new Model(type: Task::class)
    )]
    public function create(TaskCreateRequest $request): Response
    {
        $board = $this->boardRepository->findById($request->getBoardId());

        if ($board === null) {
            return new Response('', 404);
        }

        $command = new TaskCreateCommand($request->getBoardId(), $request->getTitle());

        $this->commandBus->execute($command);

        $task = $this->taskRepository->getById($command->getId());

        return $this->json(
            (new Task($task->getId()->toString(), $task->getTitle(), $task->getState(), []))->jsonSerialize()
        );
    }

    #[Route(
        '/api/v1/task/{id}',
        methods: ['PUT']
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'ID of the task',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', example: '839cf68e-4062-4259-addc-09ce5644ee52')
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: TaskUpdateRequest::class)))]
    #[OA\Tag(name: 'Task')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about task',
        content: new Model(type: Task::class)
    )]
    public function update(string $id, TaskUpdateRequest $request): Response
    {
        $task = $this->taskRepository->findById($id);

        if ($task === null) {
            return new Response('', 404);
        }

        $this->commandBus->execute(
            new TaskUpdateCommand($id, $request->getTitle(), $request->getState())
        );

        $task = $this->taskRepository->getById($task->getId()->toString());

        return $this->json(
            (new Task($task->getId()->toString(), $task->getTitle(), $task->getState(), []))->jsonSerialize()
        );
    }

    #[Route(
        '/api/v1/task/{id}',
        methods: ['DELETE']
    )]
    #[OA\Tag(name: 'Task')]
    public function delete(string $id): Response
    {
        $task = $this->taskRepository->findById($id);

        if ($task === null) {
            return new Response('', 404);
        }

        $command = new TaskDeleteCommand($id);

        $this->commandBus->execute($command);

        return $this->json([]);
    }
}
