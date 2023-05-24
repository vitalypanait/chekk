<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class BoardUpdateRequest implements IdentifierInterface
{
    #[OA\Property(
        description: 'Title of the board',
        example: 'Let\'s start'
    )]
    private string $title;

    #[OA\Property(
        description: 'Type of the board',
        example: 'task'
    )]
    private string $display;

    public function __construct(string $title, string $display)
    {
        $this->title = $title;
        $this->display = $display;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }
}
