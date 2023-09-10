<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardHistoryUpdate;

use App\Module\Board\Domain\Entity\BoardVisitedHistory;
use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Board\Domain\Repository\BoardVisitedHistoryRepository;
use App\Module\Common\Command\CommandHandler;

class BoardHistoryUpdateHandler implements CommandHandler
{
    public function __construct(
        private readonly BoardIdRepository $boardIdRepository,
        private readonly BoardVisitedHistoryRepository $boardVisitedHistoryRepository,
    ) {}

    public function __invoke(BoardHistoryUpdateCommand $command): void
    {
        $boardId = $this->boardIdRepository->getById($command->getBoardId());
        $boardHistory = $this->boardVisitedHistoryRepository->findByBoardAndUser(
            $boardId,
            $command->getUser()
        );
        $owner = $boardId->getBoard()->getOwner();
        $isOwnerBoard = $owner !== null && $owner->getUserIdentifier() !== $command->getUser()->getUserIdentifier();

        if ($boardHistory === null) {
            if ($isOwnerBoard) {
                $boardHistory = new BoardVisitedHistory(
                    $command->getUser(),
                    $boardId
                );

                $this->boardVisitedHistoryRepository->save($boardHistory);
            }
        } else {
            if ($isOwnerBoard) {
                $this->boardVisitedHistoryRepository->delete($boardHistory);
            } else {
                $boardHistory->updateVisited();
            }
        }
    }
}
