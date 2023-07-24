<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Http\Voter;

use App\Module\Board\Application\Service\BoardAccessManagerInterface;
use App\Module\Board\Domain\Entity\BoardId;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BoardVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';

    public function __construct(
        private readonly BoardAccessManagerInterface $boardAccessManager
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof BoardId) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var BoardId $boardId */
        $boardId = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($boardId, $token->getUser()),
            self::EDIT => $this->canEdit($boardId, $token->getUser()),
            default => throw new LogicException(sprintf('Invalid vote type %s', $attribute))
        };
    }

    private function canView(BoardId $boardId, ?UserInterface $user): bool
    {
        if ($boardId->hasPinCode()) {
            return $this->boardAccessManager->hasAccess($boardId, $user);
        }

        return true;
    }

    private function canEdit(BoardId $boardId, ?UserInterface $user): bool
    {
        if ($boardId->isReadOnly()) {
            return false;
        }

        if ($boardId->hasPinCode()) {
            return $this->boardAccessManager->hasAccess($boardId, $user);
        }

        return true;
    }
}
