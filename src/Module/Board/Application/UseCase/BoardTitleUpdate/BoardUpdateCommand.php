<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardTitleUpdate;

class BoardUpdateCommand
{
    public function __construct(
        private string $id,
        private string $title,
        private string $display
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }
}
