<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Entity;

use App\Module\Common\Traits\Timestamped;
use App\Module\Core\Domain\Entity\User;
use DateTime;
use DomainException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Board
{
    use Timestamped;

    public const DISPLAY_TASK = 'task';
    public const DISPLAY_LIST = 'list';
    public const DISPLAY_CONTENT = 'content';

    public const THEME_LIGHT = 'light';
    public const THEME_DARK = 'dark';

    private UuidInterface $id;

    private ?string $title = null;

    private string $display;

    private string $theme;

    private ?User $owner;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->display = self::DISPLAY_TASK;
        $this->theme = self::THEME_LIGHT;
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

    public function hasOwner(): bool
    {
        return $this->owner !== null;
    }

    public function isOwner(?UserInterface $owner): bool
    {
        if ($this->owner === null || $owner === null) {
            return false;
        }

        return $this->owner->getUserIdentifier() === $owner->getUserIdentifier();
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

    public function updateTheme(string $theme): void
    {
        if (!in_array($theme, [self::THEME_DARK, self::THEME_LIGHT])) {
            throw new DomainException(sprintf('Invalid theme %s', $theme));
        }

        $this->theme = $theme;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }
}
