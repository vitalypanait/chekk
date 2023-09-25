<?php

namespace App\Module\Common\Application\Notificator;

interface EmailNotificatorInterface
{
    public function send(string $email, string $subject, string $text): void;
}