<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use OpenApi\Attributes as OA;

class BoardPinCodeRequest implements IdentifierInterface
{
    #[OA\Property(
        description: 'Pin code',
        example: '12345678'
    )]
    private string $pinCode;

    public function __construct(string $pinCode)
    {
        $this->pinCode = $pinCode;
    }

    public function getPinCode(): string
    {
        return $this->pinCode;
    }
}
