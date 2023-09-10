<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardHistoryInit;

use App\Module\Board\Domain\Entity\BoardVisitedHistory;
use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Board\Domain\Repository\BoardVisitedHistoryRepository;
use App\Module\Common\Command\CommandHandler;

class BoardHistoryInitHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardIdRepository $boardIdRepository,
        private readonly BoardVisitedHistoryRepository $boardVisitedHistoryRepository,
    ) {}

    public function __invoke(BoardHistoryInitCommand $command): void
    {
        foreach ($this->boardIdRepository->findByIds($command->getBoardIds()) as $boardId) {
            $boardHistory = new BoardVisitedHistory(
                $command->getUser(),
                $boardId
            );

            $this->boardVisitedHistoryRepository->save($boardHistory);
        }
    }
}
