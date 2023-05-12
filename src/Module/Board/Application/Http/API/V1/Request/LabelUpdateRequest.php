<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class LabelUpdateRequest implements IdentifierInterface
{
    #[OA\Property(description: 'Label ID', example: '839cf68e-4062-4259-addc-09ce5644ee52')]
    private string $id;

    #[OA\Property(description: 'Title', example: 'Back')]
    private string $title;

    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
