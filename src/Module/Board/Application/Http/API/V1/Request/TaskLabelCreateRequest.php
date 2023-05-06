<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class TaskLabelCreateRequest implements IdentifierInterface
{
    #[OA\Property(description: 'Task ID', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $taskId;

    #[OA\Property(description: 'Label ID', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $labelId;

    public function __construct(string $taskId, string $labelId)
    {
        $this->taskId = $taskId;
        $this->labelId = $labelId;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getLabelId(): string
    {
        return $this->labelId;
    }
}
