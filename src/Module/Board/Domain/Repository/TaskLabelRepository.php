<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Repository;

use App\Module\Board\Domain\Entity\Board;
use App\Module\Board\Domain\Entity\TaskLabel;

interface TaskLabelRepository
{
    public function save(TaskLabel $taskLabel): void;

    public function delete(TaskLabel $taskLabel): void;

    public function getById(string $id): TaskLabel;

    /**
     * @return TaskLabel[]
     */
    public function findByBoard(Board $board): array;
}