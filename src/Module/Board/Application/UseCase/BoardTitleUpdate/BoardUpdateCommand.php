<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardTitleUpdate;

class BoardUpdateCommand
{
    public function __construct(
        private readonly string $id,
        private readonly ?string $title = null,
        private readonly ?string $display = null,
        private readonly ?string $themeColor = null
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDisplay(): ?string
    {
        return $this->display;
    }

    public function getThemeColor(): ?string
    {
        return $this->themeColor;
    }
}
