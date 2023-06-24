<?php

namespace App\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
final class AuthenticationListener
{
    public function __construct(private ManagerRegistry $em)
    {}
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $user = $event->getUser();

        $user->setLastLoggedIn(new \DateTime());
        $this->em->getManager()->flush();
    }
}