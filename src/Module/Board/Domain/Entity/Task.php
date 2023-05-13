<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Entity;

use App\Module\Common\Traits\Timestamped;
use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Task
{
    use Timestamped;

    public const STATE_CREATED = 'created';
    public const STATE_PROCESSING = 'processing';
    public const STATE_COMPLETED = 'completed';
    public const STATE_PAUSED = 'paused';

    private UuidInterface $id;

    public function __construct(
        private readonly Board $board,
        private string         $title,
        private int            $position,
        private string         $state = self::STATE_CREATED,
        private ?DateTime      $archivedAt = null
    ) {
        $this->id = Uuid::uuid4();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function update(string $title, string $state): void
    {
        $this->title = $title;
        $this->state = $state;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getBoard(): Board
    {
        return $this->board;
    }

    public function updatePosition(int $position): void
    {
        $this->position = $position;
    }

    public function moveToArchive(): void
    {
        $this->archivedAt = new DateTime();
    }

    public function removeFromArchive(): void
    {
        $this->archivedAt = null;
    }
}