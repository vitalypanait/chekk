<?php

declare(strict_types=1);

namespace App\Module\Board\Infrastructure\Service;

use App\Module\Board\Application\Service\BoardsCookieJar as BoardsKeeperInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class BoardsCookieJar implements BoardsKeeperInterface
{
    private const MY_BOARDS = 'my_boards';

    public function __construct(
        private readonly RequestStack $requestStack
    ) {}

    public function addBoard(string $id, Response $response): void
    {
        $boards = $this->all();

        if (in_array($id, $this->all())) {
            return;
        }

        $boards[] = $id;

        $response->headers->setCookie(
            new Cookie(self::MY_BOARDS, json_encode($boards))
        );
    }

    public function all(): array
    {
        $cookies = $this->requestStack->getCurrentRequest()->cookies;

        return $cookies->has(self::MY_BOARDS) ? json_decode($cookies->get(self::MY_BOARDS), true) : [];
    }
}
