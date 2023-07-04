<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\BodyRendererInterface;

class MailerService
{
    public function __construct(private MailerInterface $mailer, private BodyRendererInterface $bodyRenderer)
    {}

    public function sendResetPasswordEmail(User $user, string $urlLink)
    {
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail()))
            ->subject('Vous avez oublié votre mot de passe')
            ->htmlTemplate('email/reset_password_email.html.twig')
            ->context([
                'urlLink' => $urlLink,
            ]);
        $this->bodyRenderer->render($email);
        $this->mailer->send($email);
    }
}