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
        description: 'Board display',
        example: 'task'
    )]
    private string $display;

    #[OA\Property(
        description: 'Board theme',
        example: 'light'
    )]
    private string $theme;

    public function __construct(string $title, string $display, string $theme)
    {
        $this->title = $title;
        $this->display = $display;
        $this->theme = $theme;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }
}
