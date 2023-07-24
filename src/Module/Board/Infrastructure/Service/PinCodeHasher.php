<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Service;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class PinCodeHasher
{
    public function getHasher(): PasswordHasherInterface
    {
        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'sodium' => ['algorithm' => 'sodium'],
        ]);

        return $factory->getPasswordHasher('common');
    }
}