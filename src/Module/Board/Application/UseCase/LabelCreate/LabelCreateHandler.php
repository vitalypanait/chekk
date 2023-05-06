<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\LabelCreate;

use App\Module\Board\Domain\Entity\Label;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Board\Domain\Repository\LabelRepository;
use App\Module\Common\Command\CommandHandler;

class LabelCreateHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardRepository $boardRepository,
        private readonly LabelRepository $labelRepository
    ) {}

    public function __invoke(LabelCreateCommand $command): void
    {
        $board = $this->boardRepository->getById($command->getBoardId());

        $label = new Label($board, $command->getTitle(), $command->getColor());

        $this->labelRepository->save($label);

        $command->setId($label->getId()->toString());
    }
}
