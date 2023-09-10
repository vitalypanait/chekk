<?php

declare(strict_types=1);

namespace App\Module\Core\Application\Subscriber;

use App\Module\Board\Application\Service\BoardsCookieJar;
use App\Module\Board\Application\UseCase\BoardHistoryInit\BoardHistoryInitCommand;
use App\Module\Board\Domain\Entity\BoardId;
use App\Module\Board\Domain\Repository\BoardIdRepository;
use App\Module\Common\Bus\CommandBus;
use App\Module\Core\Domain\Entity\User;
use App\Module\Core\Domain\Event\UserCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class KeepUserHistorySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly BoardsCookieJar $boardsCookieJar,
        private readonly BoardIdRepository $boardIdRepository,
        private readonly CommandBus $commandBus,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreated::class => 'onEvent',
        ];
    }

    public function onEvent(UserCreated $event): void
    {
        /** @var User $user */
        $user = $event->getEntity();
        $visitedBoardIds = $this->boardsCookieJar->all();

        if (empty($visitedBoardIds)) {
            return;
        }

        $boardIds = array_map(
            fn (BoardId $boardId) => $boardId->getId()->toString(),
            $this->boardIdRepository->findByOwner($user->getUserIdentifier())
        );

        $boardIdsHistory = [];

        foreach ($this->boardsCookieJar->all() as $boardId) {
            if (!in_array($boardId, $boardIds)) {
                $boardIdsHistory[$boardId] = $boardId;
            }
        }

        $boardIdsHistory = array_keys($boardIdsHistory);

        if (!empty($boardIdsHistory)) {
            $this->commandBus->execute(new BoardHistoryInitCommand($boardIdsHistory, $user));
        }
    }
}
