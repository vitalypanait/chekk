<?php

namespace App\Module\Core\Domain\Service;

interface PasswordGeneratorInterface
{
    public function generate(): string;
}