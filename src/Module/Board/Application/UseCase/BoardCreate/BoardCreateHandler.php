<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardCreate;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Common\Command\CommandHandler;

class BoardCreateHandler implements CommandHandler
{
    public function __construct(private readonly BoardRepository $boardRepository) {}

    public function __invoke(BoardCreateCommand $command): void
    {
        $board = new Board();

        $this->boardRepository->save($board);

        $command->setId($board->getId());
    }
}
