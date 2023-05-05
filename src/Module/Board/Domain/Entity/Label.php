<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Entity;

use App\Module\Common\Traits\Timestamped;
use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Label
{
    use Timestamped;

    private UuidInterface $id;

    public function __construct(
        private readonly Board $board,
        private readonly string $title
    ) {
        $this->id = Uuid::uuid4();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBoard(): Board
    {
        return $this->board;
    }
}