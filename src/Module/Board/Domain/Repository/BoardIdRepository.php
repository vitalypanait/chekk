<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Entity\BoardId;

interface BoardIdRepository
{
    public function save(BoardId $boardId): void;

    public function delete(BoardId $boardId): void;

    public function findById(string $id): ?BoardId;

    public function getById(string $id): BoardId;

    /**
     * @param Board $board
     * @return BoardId[]
     */
    public function findByBoard(Board $board): array;

    public function getReadonlyByBoard(Board $board): BoardId;

    /**
     * @param string[] $ids
     * @return BoardId[]
     */
    public function findByIds(array $ids): array;

    /**
     * @return BoardId[]
     */
    public function findByOwner(string $email): array;
}
