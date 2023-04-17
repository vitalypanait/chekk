<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class TaskUpdateRequest implements IdentifierInterface
{
    #[OA\Property(description: 'Title of the task', example: 'Let\'s start')]
    private string $title;

    #[OA\Property(description: 'State of the task', example: 'completed')]
    private string $state;

    public function __construct(string $title, string $state)
    {
        $this->title = $title;
        $this->state = $state;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getState(): string
    {
        return $this->state;
    }
}
