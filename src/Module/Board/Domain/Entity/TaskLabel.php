<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Entity;

use App\Module\Common\Traits\Timestamped;
use DateTime;
use DomainException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TaskLabel
{
    use Timestamped;

    private UuidInterface $id;

    public function __construct(
        private readonly Task $task,
        private readonly Label $label
    ) {
        $this->id = Uuid::uuid4();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();

        if (!$this->task->getBoard()->getId()->equals($this->label->getBoard()->getId())) {
            throw new DomainException(sprintf('The label is not from this board'));
        }
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }
}