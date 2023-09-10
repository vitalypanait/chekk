<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardHistoryInit;

use App\Module\Core\Domain\Entity\User;

class BoardHistoryInitCommand
{
    public function __construct(
        private readonly array $boardIds,
        private readonly User $user
    ) {}

    /**
     * @return string[]
     */
    public function getBoardIds(): array
    {
        return $this->boardIds;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
