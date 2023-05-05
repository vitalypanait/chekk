<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskLabelDelete;

use App\Module\Board\Domain\Repository\TaskLabelRepository;
use App\Module\Common\Command\CommandHandler;

class TaskLabelDeleteHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskLabelRepository $taskLabelRepository
    ) {}

    public function __invoke(TaskLabelDeleteCommand $command): void
    {
        $task = $this->taskLabelRepository->getById($command->getId());

        $this->taskLabelRepository->delete($task);
    }
}
