<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskUpdate;

use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Command\CommandHandler;

class TaskUpdateHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {}

    public function __invoke(TaskUpdateCommand $command): void
    {
        $task = $this->taskRepository->getById($command->getId());

        $task->update($command->getTitle(), $command->getState());
    }
}