<?php

namespace App\Module\Common\Application\Notificator;

interface EmailNotificatorInterface
{
    public function sendHtml(string $email, string $subject, string $templatePath, array $context = []): void;
}