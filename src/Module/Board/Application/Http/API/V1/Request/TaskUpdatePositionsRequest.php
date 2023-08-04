<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Board\Application\Http\API\V1\Model\TaskPosition;
use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class TaskUpdatePositionsRequest implements IdentifierInterface
{
    #[OA\Property(description: 'Task positions', example: '[]')]
    private array $positions;

    public function __construct(array $positions)
    {
        $this->positions = $positions;
    }

    /**
     * @return TaskPosition[]
     */
    public function getPositions(): array
    {
        return $this->positions;
    }
}
