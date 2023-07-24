<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class LabelCreateRequest implements IdentifierInterface
{
    #[OA\Property(description: 'Title', example: 'Back')]
    private string $title;

    #[OA\Property(description: 'Color', example: 'red')]
    private string $color;

    public function __construct(string $title, string $color)
    {
        $this->title = $title;
        $this->color = $color;
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
