<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Entity\Task;

interface TaskRepository
{
    public function save(Task $task): void;

    public function delete(Task $task): void;

    public function findById(string $id): ?Task;

    public function getById(string $id): Task;

    /**
     * @return Task[]
     */
    public function findActiveByBoard(Board $board): array;

    /**
     * @return Task[]
     */
    public function findArchivedByBoard(Board $board): array;

    public function getMaxPosition(Board $board): int;

    /**
     * @return Task[]
     */
    public function findTasksForUpdatePositions(string $boardId, array $taskIds): array;
}
