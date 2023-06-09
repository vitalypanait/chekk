<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Entity;

use App\Module\Common\Traits\Timestamped;
use App\Module\Core\Domain\Entity\User;
use DateTime;
use DomainException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Board
{
    use Timestamped;

    public const DISPLAY_TASK = 'task';
    public const DISPLAY_LIST = 'list';
    public const DISPLAY_CONTENT = 'content';

    private UuidInterface $id;

    private ?UuidInterface $readOnlyId;

    private ?string $title = null;

    private string $display;

    private ?User $owner;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->readOnlyId = Uuid::uuid4();
        $this->display = self::DISPLAY_TASK;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->owner = null;
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

    public function getDisplay(): string
    {
        return $this->display;
    }

    public function updateDisplay(string $display): void
    {
        if (! in_array($display, [self::DISPLAY_TASK, self::DISPLAY_CONTENT, self::DISPLAY_LIST])) {
            throw new DomainException(sprintf('Undefined type %s', $display));
        }

        $this->display = $display;
    }

    public function isReadOnly(UuidInterface $uuid): bool
    {
        return $uuid->equals($this->readOnlyId);
    }

    public function getReadOnlyId(): UuidInterface
    {
        return $this->readOnlyId;
    }

    public function hasReadOnly(): bool
    {
        return $this->readOnlyId !== null;
    }

    public function setReadOnly(): void
    {
        if (!$this->hasReadOnly()) {
            $this->readOnlyId = Uuid::uuid4();
        }
    }

    public function hasOwner(): bool
    {
        return $this->owner !== null;
    }

    public function takeOwnership(User $owner): void
    {
        if ($this->owner !== null) {
            throw new DomainException(sprintf('Board %s already has owner', $this->id->toString()));
        }

        $this->owner = $owner;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }
}
