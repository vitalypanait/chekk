<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\LabelCreate;

use App\Module\Board\Domain\Entity\Label;
use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Board\Domain\Repository\LabelRepository;
use App\Module\Common\Command\CommandHandler;

class LabelCreateHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardIdRepository $boardIdRepository,
        private readonly LabelRepository $labelRepository
    ) {}

    public function __invoke(LabelCreateCommand $command): void
    {
        $boardId = $this->boardIdRepository->getById($command->getBoardId());

        $label = new Label($boardId->getBoard(), $command->getTitle(), $command->getColor());

        $this->labelRepository->save($label);

        $command->setId($label->getId()->toString());
    }
}
