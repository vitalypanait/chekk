<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Repository;

use App\Module\Board\Domain\Entity\Board;
use Ramsey\Uuid\UuidInterface;

interface BoardRepository
{
    public function save(Board $board): void;

    public function findById(string $id): ?Board;

    public function getById(string $id): Board;
}
