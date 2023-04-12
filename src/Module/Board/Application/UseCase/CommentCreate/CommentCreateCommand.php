<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\CommentCreate;

class CommentCreateCommand
{
    public function __construct(
        private readonly string $taskId,
        private readonly string $context
    ) {}

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getContext(): string
    {
        return $this->context;
    }
}