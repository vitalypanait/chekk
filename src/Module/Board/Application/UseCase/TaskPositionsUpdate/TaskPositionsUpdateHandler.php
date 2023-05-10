<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskPositionsUpdate;

use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Command\CommandHandler;

class TaskPositionsUpdateHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepository $taskRepository
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

        $tasks = $this->taskRepository->findTasksForUpdatePositions($command->getBoardId(), array_keys($positions));

        foreach ($tasks as $task) {
            $task->updatePosition($positions[$task->getId()->toString()] ?? 0);
        }
    }
}
