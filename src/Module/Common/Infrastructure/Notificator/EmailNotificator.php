<?php

declare(strict_types=1);

namespace App\Module\Common\Infrastructure\Notificator;

use App\Module\Common\Application\Notificator\EmailNotificatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificator implements EmailNotificatorInterface
{
    public function __construct(
        private readonly MailerInterface $mailer
    ){}

    public function sendHtml(string $email, string $subject, string $templatePath, array $context = []): void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@windo.us')
            ->to($email)
            ->subject($subject)
            ->htmlTemplate($templatePath);

        if (!empty($context)) {
            $email->context($context);
        }

        $this->mailer->send($email);
    }
}