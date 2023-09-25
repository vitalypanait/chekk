<?php

declare(strict_types=1);

namespace App\Module\Common\Infrastructure\Notificator;

use App\Module\Common\Application\Notificator\EmailNotificatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificator implements EmailNotificatorInterface
{
    public function __construct(
        private readonly MailerInterface $mailer
    ){}

    public function send(string $email, string $subject, string $text): void
    {
        $email = (new Email())
            ->from('no-reply@windo.us')
            ->to($email)
            ->subject($subject)
            ->text($text);

        $this->mailer->send($email);
    }
}