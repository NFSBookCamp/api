<?php

namespace App\Security;

use App\Entity\Account;
use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if ($user->getAccount()->getStatus() === Account::ACCOUNT_STATUS_DELETED) {
            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Votre compte n\'existe plus. Veuillez contacter un administrateur de l\'établissement pour en savoir plus.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
        if ($user->getAccount()->getStatus() == Account::ACCOUNT_STATUS_PENDING) {
            throw new CustomUserMessageAccountStatusException('Un administrateur de l\'établissement doit activer votre compte.');
        }
        // user account is expired, the user may be notified
        if ($user->getAccount()->getStatus() == Account::ACCOUNT_STATUS_DISABLED) {
            throw new CustomUserMessageAccountStatusException('Votre compte a été désactivé. Veuillez contacter un administrateur de l\'établissement pour en savoir plus.');
        }
    }
}

?>