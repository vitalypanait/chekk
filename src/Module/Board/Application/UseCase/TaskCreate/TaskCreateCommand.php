<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskCreate;

class TaskCreateCommand
{
    public function __construct(
        private readonly string $boardId,
        private readonly string $title
    ) {}

    public function getBoardId(): string
    {
        return $this->boardId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}