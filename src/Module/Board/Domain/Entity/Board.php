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

    public const TYPE_TASK = 'task';
    public const TYPE_LIST = 'list';
    public const TYPE_CONTENT = 'content';

    private UuidInterface $id;

    private ?string $title = null;

    private string $type;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->type = self::TYPE_TASK;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function updateType(string $type): void
    {
        if (! in_array($type, [self::TYPE_TASK, self::TYPE_CONTENT, self::TYPE_LIST])) {
            throw new DomainException(sprintf('Undefined type %s', $type));
        }

        $this->type = $type;
    }
}
