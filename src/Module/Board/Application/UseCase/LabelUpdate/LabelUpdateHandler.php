<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\LabelUpdate;

use App\Module\Board\Domain\Repository\LabelRepository;
use App\Module\Common\Command\CommandHandler;

class LabelUpdateHandler implements CommandHandler
{
    public function __construct(
        private readonly LabelRepository $labelRepository
    ) {}

    public function __invoke(LabelUpdateCommand $command): void
    {
        $label = $this->labelRepository->getById($command->getId());

        $label->updateTitle($command->getTitle());
    }
}
