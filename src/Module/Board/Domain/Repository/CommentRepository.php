<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Repository;

use App\Module\Board\Domain\Entity\Comment;

interface CommentRepository
{
    public function save(Comment $comment): void;

    /**
     * @param string[] $taskIds
     * @return Comment[]
     */
    public function findByTaskIds(array $taskIds): array;

    public function getById(string $id): Comment;
}
