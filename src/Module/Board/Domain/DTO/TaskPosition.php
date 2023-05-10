<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\DTO;

class TaskPosition
{
    public function __construct(
        private readonly string $taskId,
        private readonly int $position
    )
    {}

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}