<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardTitleUpdate;

class BoardTitleUpdateCommand
{
    public function __construct(
        private string $id,
        private string $title
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}