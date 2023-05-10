<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Model;

use OpenApi\Attributes as OA;

class TaskPosition
{
    #[OA\Property(description: 'Task ID', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $taskId;

    #[OA\Property(description: 'Task position', example: 1)]
    private int $position;

    public function __construct(string $taskId, int $position)
    {

        $this->taskId = $taskId;
        $this->position = $position;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}