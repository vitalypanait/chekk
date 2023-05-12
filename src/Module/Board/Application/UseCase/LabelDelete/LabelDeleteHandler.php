<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\LabelDelete;

use App\Module\Board\Domain\Repository\LabelRepository;
use App\Module\Board\Domain\Repository\TaskLabelRepository;
use App\Module\Common\Command\CommandHandler;

class LabelDeleteHandler implements CommandHandler
{
    public function __construct(
        private readonly LabelRepository $labelRepository,
        private readonly TaskLabelRepository $taskLabelRepository,

    ) {}

    public function __invoke(LabelDeleteCommand $command): void
    {
        $label = $this->labelRepository->getById($command->getId());

        foreach ($this->taskLabelRepository->findByLabel($label) as $taskLabel) {
            $this->taskLabelRepository->delete($taskLabel);
        }

        $this->labelRepository->delete($label);
    }
}
