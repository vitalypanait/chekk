<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1;

use App\Module\Board\Application\Http\API\V1\Model\Task;
use App\Module\Board\Application\Http\API\V1\Model\TaskPosition;
use App\Module\Board\Application\Http\API\V1\Request\TaskCreateRequest;
use App\Module\Board\Application\Http\API\V1\Request\TaskUpdatePositionsRequest;
use App\Module\Board\Application\Http\API\V1\Request\TaskUpdateRequest;
use App\Module\Board\Application\UseCase\TaskCreate\TaskCreateCommand;
use App\Module\Board\Application\UseCase\TaskDelete\TaskDeleteCommand;
use App\Module\Board\Application\UseCase\TaskMoveToArchive\TaskMoveToArchiveCommand;
use App\Module\Board\Application\UseCase\TaskPositionsUpdate\TaskPositionsUpdateCommand;
use App\Module\Board\Application\UseCase\TaskRemoveFromArchive\TaskRemoveFromArchiveCommand;
use App\Module\Board\Application\UseCase\TaskUpdate\TaskUpdateCommand;
use App\Module\Board\Domain\DTO\TaskPosition as TaskPositionDTO;
use App\Module\Board\Domain\Entity\BoardId;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Bus\CommandBus;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly CommandBus $commandBus
    ) {}

    #[Route(
        '/api/v1/board/{id}/task/',
        methods: ['POST']
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: TaskCreateRequest::class)))]
    #[OA\Tag(name: 'Task')]
    #[OA\Response(
        response: 200,
        description: 'Returns info about task',
        content: new Model(type: Task::class)
    )]
    #[IsGranted('edit', 'boardId')]
    public function create(BoardId $boardId, TaskCreateRequest $request): Response
    {
        $command = new TaskCreateCommand($boardId->getId()->toString(), $request->getTitle());

        $this->commandBus->execute($command);

        $task = $this->taskRepository->getById($command->getId());

        return $this->json(
            (new Task($task->getId()->toString(), $task->getTitle(), $task->getState(), [], []))->jsonSerialize()
        );
    }

    #[Route(
        '/api/v1/board/{id}/task/{taskId}',
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
    #[IsGranted('edit', 'boardId')]
    public function update(BoardId $boardId, string $taskId, TaskUpdateRequest $request): Response
    {
        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            return new Response('', 404);
        }

        $this->commandBus->execute(
            new TaskUpdateCommand($taskId, $request->getTitle(), $request->getState())
        );

        $task = $this->taskRepository->getById($task->getId()->toString());

        return $this->json(
            (new Task($task->getId()->toString(), $task->getTitle(), $task->getState(), [], []))->jsonSerialize()
        );
    }

    #[Route(
        '/api/v1/board/{id}/task/position/',
        methods: ['PUT']
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: TaskUpdatePositionsRequest::class)))]
    #[OA\Tag(name: 'Task')]
    #[OA\Response(
        response: 200,
        description: '',
    )]
    #[IsGranted('edit', 'boardId')]
    public function updatePositions(BoardId $boardId, TaskUpdatePositionsRequest $request): Response
    {
        $this->commandBus->execute(
            new TaskPositionsUpdateCommand(
                $boardId->getId()->toString(),
                array_map(
                    fn(TaskPosition $position) => new TaskPositionDTO($position->getTaskId(), $position->getPosition()),
                    $request->getPositions()
                )
            )
        );

        return $this->json([]);
    }

    #[Route(
        '/api/v1/board/{id}/task/archive/{taskId}',
        methods: ['PUT']
    )]
    #[OA\Tag(name: 'Task')]
    #[OA\Response(
        response: 200,
        description: '',
    )]
    #[IsGranted('edit', 'boardId')]
    public function moveToArchive(BoardId $boardId, string $taskId): Response
    {
        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            return new Response('', 404);
        }

        $this->commandBus->execute(
            new TaskMoveToArchiveCommand($taskId)
        );

        return $this->json([]);
    }

    #[Route(
        '/api/v1/board/{id}/task/archive/{taskId}',
        methods: ['DELETE']
    )]
    #[OA\Tag(name: 'Task')]
    #[OA\Response(
        response: 200,
        description: '',
    )]
    #[IsGranted('edit', 'boardId')]
    public function removeFromArchive(BoardId $boardId, string $taskId): Response
    {
        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            return new Response('', 404);
        }

        $this->commandBus->execute(
            new TaskRemoveFromArchiveCommand($taskId)
        );

        return $this->json([]);
    }

    #[Route(
        '/api/v1/board/{id}/task/{taskId}',
        methods: ['DELETE']
    )]
    #[OA\Tag(name: 'Task')]
    #[IsGranted('edit', 'boardId')]
    public function delete(BoardId $boardId, string $taskId): Response
    {
        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            return new Response('', 404);
        }

        $command = new TaskDeleteCommand($taskId);

        $this->commandBus->execute($command);

        return $this->json([]);
    }
}
