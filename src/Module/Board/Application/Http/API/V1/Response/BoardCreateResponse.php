<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Response;

use OpenApi\Attributes as OA;

class BoardCreateResponse
{
    #[OA\Property(description: 'ID of board', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
