<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Entity;

use App\Module\Common\Traits\Timestamped;
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

    private ?string $title = null;

    private string $display;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->display = self::DISPLAY_TASK;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
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
}
