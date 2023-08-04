<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Entity;

use App\Module\Common\Traits\Timestamped;
use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class BoardId
{
    use Timestamped;

    private UuidInterface $id;

    private ?string $pinCode;

    public function __construct(
        private Board $board,
        private bool $readOnly
    ) {
        $this->id = Uuid::uuid4();
        $this->pinCode = null;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getBoard(): Board
    {
        return $this->board;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    public function removePinCode(): void
    {
        $this->pinCode = null;
    }

    public function setPinCode(string $pinCode): void
    {
        $this->pinCode = $pinCode;
    }

    public function getPinCode(): ?string
    {
        return $this->pinCode;
    }

    public function hasPinCode(): bool
    {
        return $this->pinCode !== null;
    }
}
