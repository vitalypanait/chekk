<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskMoveToArchive;

class TaskMoveToArchiveCommand
{
    public function __construct(
        private readonly string $id
    ) {}

    public function getId(): string
    {
        return $this->id;
    }
}
