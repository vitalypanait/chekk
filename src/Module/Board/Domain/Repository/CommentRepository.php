<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Repository;

use App\Module\Board\Domain\Entity\Task;

interface TaskRepository
{
    public function save(Task $task): void;

    public function findById(string $id): ?Task;

    public function getById(string $id): Task;
}