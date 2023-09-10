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
        description: 'Board theme color',
        example: 'light'
    )]
    private string $themeColor;

    public function __construct(string $title, string $display, string $themeColor)
    {
        $this->title = $title;
        $this->display = $display;
        $this->themeColor = $themeColor;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }

    public function getThemeColor(): string
    {
        return $this->themeColor;
    }
}
