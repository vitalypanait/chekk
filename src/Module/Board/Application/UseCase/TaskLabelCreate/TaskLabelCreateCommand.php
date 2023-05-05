<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskLabelCreate;

class TaskLabelCreateCommand
{
    public function __construct(
        private readonly string $taskId,
        private readonly string $labelId,
        private ?string         $id = null
    ) {}

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getLabelId(): string
    {
        return $this->labelId;
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
