<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Service;

use App\Module\Board\Application\Service\BoardAccessManagerInterface;
use App\Module\Board\Domain\Entity\BoardId;
use Symfony\Component\Security\Core\User\UserInterface;

class BoardAccessManager implements BoardAccessManagerInterface
{
    public function __construct(
        private readonly PinCodeHasher $pinCodeHasher
    ) {}

    public function hasAccess(BoardId $boardId, ?UserInterface $user, ?string $pinCode): bool
    {
        if ($boardId->getPinCode() === null || $boardId->getBoard()->isOwner($user)) {
            return true;
        }

        if (null == $pinCode) {
            return false;
        }

        return $this->pinCodeHasher->getHasher()->verify(
            $boardId->getPinCode(),
            $pinCode
        );
    }
}
