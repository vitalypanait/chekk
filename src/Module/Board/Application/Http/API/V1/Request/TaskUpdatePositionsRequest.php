<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Board\Application\Http\API\V1\Model\TaskPosition;
use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class TaskUpdatePositionsRequest implements IdentifierInterface
{
    #[OA\Property(description: 'Board ID', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $boardId;

    #[OA\Property(description: 'Task positions', example: '[]')]
    private array $positions;

    public function __construct(string $boardId, array $positions)
    {
        $this->boardId = $boardId;
        $this->positions = $positions;
    }

    public function getBoardId(): string
    {
        return $this->boardId;
    }

    /**
     * @return TaskPosition[]
     */
    public function getPositions(): array
    {
        return $this->positions;
    }
}
