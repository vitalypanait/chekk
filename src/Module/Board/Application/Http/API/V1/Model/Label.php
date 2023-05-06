<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Model;

use OpenApi\Attributes as OA;

class Label implements \JsonSerializable
{
    #[OA\Property(description: 'ID of the label', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $id;

    #[OA\Property(description: 'Title', example: 'Back')]
    private string $title;

    #[OA\Property(description: 'Color', example: 'red')]
    private string $color;

    public function __construct(string $id, string $title, string $color)
    {
        $this->id = $id;
        $this->title = $title;
        $this->color = $color;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'color' => $this->color
        ];
    }
}
