<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\CommentUpdate;

class CommentUpdateCommand
{
    public function __construct(
        private readonly string $id,
        private readonly string $content,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
