<?php

declare(strict_types=1);

namespace App\Module\Board\Application\DTO;

use App\Module\Board\Domain\Entity\Board as BoardEntity;

class Board
{
    public function __construct(
        private readonly BoardEntity $board,
        private readonly bool $readOnly
    ) {}

    public function getBoard(): BoardEntity
    {
        return $this->board;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }
}