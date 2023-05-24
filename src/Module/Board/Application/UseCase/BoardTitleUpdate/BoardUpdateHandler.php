<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardTitleUpdate;

use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Common\Command\CommandHandler;

class BoardUpdateHandler implements CommandHandler
{
    public function __construct(private readonly BoardRepository $boardRepository) {}

    public function __invoke(BoardUpdateCommand $command): void
    {
        $board = $this->boardRepository->getById($command->getId());

        $board->updateTitle($command->getTitle());
        $board->updateDisplay($command->getDisplay());
    }
}
