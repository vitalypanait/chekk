<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Service;

use App\Module\Board\Application\Service\BoardAccessManagerInterface;
use App\Module\Board\Application\Service\PinCodeAccessInvalidException;
use App\Module\Board\Domain\Entity\BoardId;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class BoardAccessManager implements BoardAccessManagerInterface
{
    private const BOARDS_KEY = 'access_boards';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly PinCodeHasher $pinCodeHasher
    ) {}
    public function hasAccess(BoardId $boardId, ?UserInterface $user): bool
    {
        if ($boardId->getPinCode() === null) {
            return true;
        }

        if ($boardId->getBoard()->isOwner($user)) {
            return true;
        }

        $boardIds = $this->requestStack->getSession()->get(self::BOARDS_KEY);
        $boardIds = empty($boardIds) ? [] : $boardIds;

        if (array_key_exists($boardId->getId()->toString(), $boardIds)) {
            $isEqual = $this->pinCodeHasher->getHasher()->verify(
                $boardId->getPinCode(),
                $boardIds[$boardId->getId()->toString()]
            );

            if (!$isEqual) {
                unset($boardIds[$boardId->getId()->toString()]);

                $this->requestStack->getSession()->set(
                    self::BOARDS_KEY,
                    $boardIds
                );
            }

            return $isEqual;
        } else {
            return false;
        }
    }

    /**
     * @throws PinCodeAccessInvalidException
     */
    public function keep(BoardId $boardId, string $pinCode): void
    {
        if ($boardId->getPinCode() === null) {
            return;
        }

        $isEqual = $this->pinCodeHasher->getHasher()->verify(
            $boardId->getPinCode(),
            $pinCode
        );

        if ($isEqual) {
            $boardIds = $this->requestStack->getSession()->get(self::BOARDS_KEY);
            $boardIds = empty($boardIds) ? [] : $boardIds;

            $this->requestStack->getSession()->set(
                self::BOARDS_KEY,
                array_merge($boardIds, [$boardId->getId()->toString() => $pinCode])
            );
        } else {
            throw new PinCodeAccessInvalidException();
        }
    }

    public function clear(): void
    {
        $this->requestStack->getSession()->remove(self::BOARDS_KEY);
    }
}