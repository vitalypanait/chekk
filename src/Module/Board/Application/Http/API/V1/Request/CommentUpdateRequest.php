<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class CommentUpdateRequest implements IdentifierInterface
{
    #[OA\Property(description: 'Text of the comment', example: 'Let\'s start')]
    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
