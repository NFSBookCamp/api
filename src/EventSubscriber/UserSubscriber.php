<?php

namespace App\EventSubscriber;

use App\Event\UpdateUserEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly JWTTokenManagerInterface $tokenManager,
    )
    {}

    public function onUpdateUser(UpdateUserEvent $event): ?string
    {
        $user = $event->getUser();
        $token = $this->tokenManager->create($user);
        return $token;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UpdateUserEvent::NAME => 'onUpdateUser',
        ];
    }

}
