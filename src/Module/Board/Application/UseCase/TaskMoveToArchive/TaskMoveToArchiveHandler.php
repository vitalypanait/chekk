<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskMoveToArchive;

use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Command\CommandHandler;

class TaskMoveToArchiveHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {}

    public function __invoke(TaskMoveToArchiveCommand $command): void
    {
        $task = $this->taskRepository->getById($command->getId());

        $task->moveToArchive();
    }
}
