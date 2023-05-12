<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\LabelUpdate;

class LabelUpdateCommand
{
    public function __construct(
        private readonly string $id,
        private readonly string $title,
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
