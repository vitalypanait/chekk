<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class TaskCreateRequest implements IdentifierInterface
{
    #[OA\Property(description: 'Board ID', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $boardId;

    #[OA\Property(description: 'Task title', example: 'First task')]
    private string $title;

    public function __construct(string $boardId, string $title)
    {
        $this->boardId = $boardId;
        $this->title = $title;
    }

    public function getBoardId(): string
    {
        return $this->boardId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
