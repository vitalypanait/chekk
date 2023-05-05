<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\LabelDelete;

use App\Module\Board\Domain\Repository\LabelRepository;
use App\Module\Common\Command\CommandHandler;

class LabelDeleteHandler implements CommandHandler
{
    public function __construct(
        private readonly LabelRepository $labelRepository
    ) {}

    public function __invoke(LabelDeleteCommand $command): void
    {
        $task = $this->labelRepository->getById($command->getId());

        $this->labelRepository->delete($task);
    }
}
