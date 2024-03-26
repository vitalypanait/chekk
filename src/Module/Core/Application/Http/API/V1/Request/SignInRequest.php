<?php

declare(strict_types=1);

namespace App\Module\Core\Application\Http\API\V1\Request;

use App\Module\Common\Infrastructure\Request\IdentifierInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SignInRequest implements IdentifierInterface
{
    #[Assert\Email()]
    private readonly string $email;

    public function __construct(string $email) {

        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
