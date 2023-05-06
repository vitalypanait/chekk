<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class LabelCreateRequest implements IdentifierInterface
{
    #[OA\Property(description: 'Board ID', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $boardId;

    #[OA\Property(description: 'Title', example: 'Back')]
    private string $title;

    #[OA\Property(description: 'Color', example: 'red')]
    private string $color;

    public function __construct(string $boardId, string $title, string $color)
    {
        $this->boardId = $boardId;
        $this->title = $title;
        $this->color = $color;
    }

    public function getBoardId(): string
    {
        return $this->boardId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
