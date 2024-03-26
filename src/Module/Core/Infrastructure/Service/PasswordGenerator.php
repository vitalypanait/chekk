<?php

namespace App\Module\Core\Infrastructure\Service;

use App\Module\Core\Domain\Service\PasswordGeneratorInterface;

class PasswordGenerator implements PasswordGeneratorInterface
{
    public function generate(): string
    {
        return (string) rand(10000, 99999);
    }
}
