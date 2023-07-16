<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Service;

use App\Module\Board\Domain\Entity\BoardId;

interface ReadOnlyBoardKeeper
{
    public function keep(BoardId $boardId): void;

    public function exists(BoardId $boardId): bool;
}
