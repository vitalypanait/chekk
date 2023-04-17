<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Model;

use OpenApi\Attributes as OA;

class Comment implements \JsonSerializable
{
    #[OA\Property(description: 'ID of the task', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $id;

    #[OA\Property(description: 'Content', example: 'Content of the comment')]
    private string $content;

    public function __construct(string $id, string $content)
    {
        $this->id = $id;
        $this->content = $content;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
        ];
    }
}
