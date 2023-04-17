<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\CommentCreate;

class CommentCreateCommand
{
    public function __construct(
        private readonly string $taskId,
        private readonly string $content,
        private ?string $id = null
    ) {}

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
