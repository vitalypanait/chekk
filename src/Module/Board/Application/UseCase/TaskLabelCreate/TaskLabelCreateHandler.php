<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskLabelCreate;

use App\Module\Board\Domain\Entity\Task;
use App\Module\Board\Domain\Entity\TaskLabel;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Repository\LabelRepository;
use App\Module\Board\Domain\Repository\TaskLabelRepository;
use App\Module\Board\Domain\Repository\TaskRepository;
use App\Module\Common\Command\CommandHandler;

class TaskLabelCreateHandler implements CommandHandler
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly LabelRepository $labelRepository,
        private readonly TaskLabelRepository $taskLabelRepository,
    ) {}

    public function __invoke(TaskLabelCreateCommand $command): void
    {
        $task = $this->taskRepository->getById($command->getTaskId());
        $label = $this->labelRepository->getById($command->getLabelId());

        $taskLabel = new TaskLabel($task, $label);

        $this->taskLabelRepository->save($taskLabel);

        $command->setId($taskLabel->getId()->toString());
    }
}
