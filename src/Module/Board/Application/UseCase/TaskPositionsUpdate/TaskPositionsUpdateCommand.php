<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\TaskPositionsUpdate;

use App\Module\Board\Domain\DTO\TaskPosition;

class TaskPositionsUpdateCommand
{
    public function __construct(
        private readonly string $boardId,
        private readonly array $positions
    ) {}

    public function getBoardId(): string
    {
        return $this->boardId;
    }

    /**
     * @return TaskPosition[]
     */
    public function getPositions(): array
    {
        return $this->positions;
    }
}
