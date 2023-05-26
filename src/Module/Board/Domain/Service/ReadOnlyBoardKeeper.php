<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Service;

use App\Module\Board\Domain\Entity\Board;

interface ReadOnlyBoardKeeper
{
    public function addBoard(Board $board): void;

    public function exists(Board $board): bool;

    public function removeBoard(Board $board): void;
}
