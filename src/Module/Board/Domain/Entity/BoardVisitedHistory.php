<?php

declare(strict_types=1);

namespace App\Module\Board\Domain\Entity;

use App\Module\Common\Traits\Timestamped;
use App\Module\Core\Domain\Entity\User;
use DateTime;
use DateTimeInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class BoardVisitedHistory
{
    use Timestamped;

    private UuidInterface $id;

    private DateTimeInterface $visitedAt;

    public function __construct(
       private User $user,
       private BoardId $boardId
    )
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new DateTime();
        $this->visitedAt = new DateTime();
    }

    public function getBoardId(): BoardId
    {
        return $this->boardId;
    }

    public function updateVisited(): void
    {
        $this->visitedAt = new DateTime();
    }
}
