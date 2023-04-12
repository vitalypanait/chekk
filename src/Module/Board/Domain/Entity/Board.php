<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Entity;

use App\Module\Common\Traits\Timestamped;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Board
{
    use Timestamped;

    private UuidInterface $id;

    private ?string $title = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function updateTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
}