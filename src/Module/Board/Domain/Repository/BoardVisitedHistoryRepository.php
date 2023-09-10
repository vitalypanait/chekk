<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Repository;

use App\Module\Board\Domain\Entity\BoardId;
use App\Module\Board\Domain\Entity\BoardVisitedHistory;
use App\Module\Core\Domain\Entity\User;

interface BoardVisitedHistoryRepository
{
    public function save(BoardVisitedHistory $boardVisitedHistory): void;

    public function findById(string $id): ?BoardVisitedHistory;

    public function findByOwner(string $email): array;

    public function delete(BoardVisitedHistory $boardVisitedHistory): void;

    public function findByBoardAndUser(BoardId $boardId, User $user): ?BoardVisitedHistory;
}
