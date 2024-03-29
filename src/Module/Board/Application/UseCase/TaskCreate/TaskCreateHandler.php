<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskCreate;

use App\Module\Board\Domain\Entity\Task;
use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Command\CommandHandler;

class TaskCreateHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardIdRepository $boardIdRepository,
        private readonly TaskRepository $taskRepository
    ) {}

    public function __invoke(TaskCreateCommand $command): void
    {
        $boardId = $this->boardIdRepository->getById($command->getBoardId());
        $lastPosition = $this->taskRepository->getMaxPosition($boardId->getBoard());

        $task = new Task($boardId->getBoard(), $command->getTitle(), ++$lastPosition);

        $this->taskRepository->save($task);

        $command->setId($task->getId()->toString());
    }
}
