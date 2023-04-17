<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class CommentCreateRequest implements IdentifierInterface
{
    #[OA\Property(description: 'Task ID', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $taskId;

    #[OA\Property(description: 'Content of comment', example: 'Any text')]
    private string $content;

    public function __construct(
        string $taskId,
        string $content
    )
    {
        $this->taskId = $taskId;
        $this->content = $content;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
