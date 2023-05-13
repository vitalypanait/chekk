<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskRemoveFromArchive;

class TaskRemoveFromArchiveCommand
{
    public function __construct(
        private readonly string $id
    ) {}

    public function getId(): string
    {
        return $this->id;
    }
}