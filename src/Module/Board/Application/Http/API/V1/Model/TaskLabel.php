<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Model;

use OpenApi\Attributes as OA;

class TaskLabel implements \JsonSerializable
{
    #[OA\Property(description: 'ID of the label in the task', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $id;

    #[OA\Property(description: 'ID of the task', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $taskId;

    #[OA\Property(description: 'label')]
    private Label $label;

    public function __construct(string $id, string $taskId, Label $label)
    {
        $this->id = $id;
        $this->taskId = $taskId;
        $this->label = $label;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'taskId' => $this->taskId,
            'label' => $this->label->jsonSerialize(),
        ];
    }
}
