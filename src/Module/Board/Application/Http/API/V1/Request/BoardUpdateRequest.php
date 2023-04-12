<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;

readonly class BoardUpdateRequest implements IdentifierInterface
{
    public function __construct(
        private string $title
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }
}