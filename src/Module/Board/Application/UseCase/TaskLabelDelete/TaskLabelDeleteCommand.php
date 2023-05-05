<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskLabelDelete;

class TaskLabelDeleteCommand
{
    public function __construct(
        private string $id
    ) {}

    public function getId(): string
    {
        return $this->id;
    }
}
