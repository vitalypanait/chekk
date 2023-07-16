<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardDelete;

use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Board\Domain\Repository\BoardRepository;
use App\Module\Common\Command\CommandHandler;

class BoardDeleteHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardIdRepository $boardIdRepository,
        private readonly BoardRepository $boardRepository
    ) {}

    public function __invoke(BoardDeleteCommand $command): void
    {
        $boardId = $this->boardIdRepository->getById($command->getId());
        $board = $boardId->getBoard();

        foreach ($this->boardIdRepository->findByBoard($board) as $boardId) {
            $this->boardIdRepository->delete($boardId);
        }

        $this->boardRepository->delete($board);
    }
}
