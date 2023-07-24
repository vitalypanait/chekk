<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Service;

use App\Module\Board\Domain\Entity\BoardId;
use Symfony\Component\Security\Core\User\UserInterface;

interface BoardAccessManagerInterface
{
    public function hasAccess(BoardId $boardId, ?UserInterface $user): bool;

    /**
     * @throws PinCodeAccessInvalidException
     */
    public function keep(BoardId $boardId, string $pinCode): void;

    public function clear(): void;
}