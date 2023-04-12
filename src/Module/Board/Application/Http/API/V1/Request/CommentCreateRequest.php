<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;

readonly class CommentCreateRequest implements IdentifierInterface
{
    public function __construct(
        private string $taskId,
        private string $content
    ) {}

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}