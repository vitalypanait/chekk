<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskUpdate;

class TaskUpdateCommand
{
    public function __construct(
        private readonly string $id,
        private readonly string $title,
        private readonly string $state,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getState(): string
    {
        return $this->state;
    }
}
