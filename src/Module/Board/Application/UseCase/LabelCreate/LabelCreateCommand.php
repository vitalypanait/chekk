<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\LabelCreate;

class LabelCreateCommand
{
    public function __construct(
        private readonly string $boardId,
        private readonly string $title,
        private readonly string $color,
        private ?string $id = null
    ) {}

    public function getBoardId(): string
    {
        return $this->boardId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getColor(): string
    {
        return $this->color;
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
