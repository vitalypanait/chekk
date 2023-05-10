<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskCreate;

use App\Module\Board\Domain\Entity\Task;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Command\CommandHandler;

class TaskCreateHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardRepository $boardRepository,
        private readonly TaskRepository $taskRepository
    ) {}

    public function __invoke(TaskCreateCommand $command): void
    {
        $board = $this->boardRepository->getById($command->getBoardId());
        $lastPosition = $this->taskRepository->getMaxPosition($board);

        $task = new Task($board, $command->getTitle(), ++$lastPosition);

        $this->taskRepository->save($task);

        $command->setId($task->getId()->toString());
    }
}
