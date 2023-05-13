<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskRemoveFromArchive;

use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Command\CommandHandler;

class TaskRemoveFromArchiveHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {}

    public function __invoke(TaskRemoveFromArchiveCommand $command): void
    {
        $task = $this->taskRepository->getById($command->getId());

        $task->removeFromArchive();
    }
}
