<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;

readonly class TaskCreateRequest implements IdentifierInterface
{
    public function __construct(
        private string $boardId,
        private string $title
    ) {}

    public function getBoardId(): string
    {
        return $this->boardId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}