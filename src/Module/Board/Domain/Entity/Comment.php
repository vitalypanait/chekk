<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Entity;

use App\Module\Common\Traits\Timestamped;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Comment
{
    use Timestamped;

    private UuidInterface $id;

    public function __construct(
        private readonly Task $task,
        private string $content
    ) {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}