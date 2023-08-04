<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskPositionsUpdate;

use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Command\CommandHandler;

class TaskPositionsUpdateHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly BoardIdRepository $boardIdRepository
    ) {}

    public function __invoke(TaskPositionsUpdateCommand $command): void
    {
        if (empty($command->getPositions())) {
            return;
        }

        $positions = [];

        foreach ($command->getPositions() as $position) {
            $positions[$position->getTaskId()] = $position->getPosition();
        }

        $boardId = $this->boardIdRepository->getById($command->getBoardId());

        $tasks = $this->taskRepository->findTasksForUpdatePositions(
            $boardId->getBoard()->getId()->toString(),
            array_keys($positions)
        );

        foreach ($tasks as $task) {
            $task->updatePosition($positions[$task->getId()->toString()] ?? 0);
        }
    }
}
