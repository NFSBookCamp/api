<?php

namespace App\EventListener;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function __construct(private UserRepository $userRepository) {}

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $user = $this->userRepository->findOneBy(['email' => $payload['username']]);

        $account = $user->getAccount();

        $payload['account'] = $account->getData();

        $event->setData($payload);
    }
}