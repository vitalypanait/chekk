<?php

declare(strict_types=1);

namespace App\Module\Board\Application\UseCase\BoardSetPinCode;

class BoardSetPinCodeCommand
{
    public function __construct(
        private string $id,
        private string $pinCode
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getPinCode(): string
    {
        return $this->pinCode;
    }
}
