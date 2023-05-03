<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskDelete;

use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Command\CommandHandler;

class TaskDeleteHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {}

    public function __invoke(TaskDeleteCommand $command): void
    {
        $task = $this->taskRepository->getById($command->getId());

        $this->taskRepository->delete($task);
    }
}
