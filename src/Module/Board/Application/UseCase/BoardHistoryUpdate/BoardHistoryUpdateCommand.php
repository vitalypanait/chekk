<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardHistoryUpdate;

use App\Module\Core\Domain\Entity\User;

class BoardHistoryUpdateCommand
{
    public function __construct(
        private readonly string $boardId,
        private readonly User $user
    ) {}

    public function getBoardId(): string
    {
        return $this->boardId;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
