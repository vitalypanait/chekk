<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Repository;

use App\Module\Board\Domain\Entity\Board;
use Ramsey\Uuid\UuidInterface;

interface BoardRepository
{
    public function save(Board $board): void;

    public function findById(string $id): ?Board;

    public function findReadOnlyById(string $id): ?Board;

    public function getById(string $id): Board;

    /**
     * @param string[] $ids
     * @return Board[]
     */
    public function findByIds(array $ids): array;

    public function delete(Board $board): void;
}
