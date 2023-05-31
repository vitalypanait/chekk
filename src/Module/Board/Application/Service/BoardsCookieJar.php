<?php

declare(strict_types=1);

namespace App\Module\Board\Application\Service;

use Symfony\Component\HttpFoundation\Response;

interface BoardsCookieJar
{
    public function addBoard(string $id, Response $response): void;

    /**
     * @return string[]
     */
    public function all(): array;
}
