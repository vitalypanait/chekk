<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardReadOnlyCreate;

use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Common\Command\CommandHandler;

class BoardReadOnlyCreateHandler implements CommandHandler
{
    public function __construct(private readonly BoardRepository $boardRepository) {}

    public function __invoke(BoardReadOnlyCreateCommand $command): void
    {
        $board = $this->boardRepository->getById($command->getId());

        $board->setReadOnly();
    }
}
