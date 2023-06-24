<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService
{
    public function __construct(private MailerInterface $mailer)
    {}

    public function sendResetPasswordEmail(User $user, string $urlLink)
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->subject('T\as zappé ton mot de passe et t\'as demandé à le changer')
            ->htmlTemplate('email/reset_password_email.html.twig')
            ->context([
                'urlLink' => $urlLink,
            ]);
        $this->mailer->send($email);
    }
}