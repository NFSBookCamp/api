<?php

namespace App\Event;

use App\Entity\User;

class UpdateUserEvent
{
    public const NAME = 'user.update';

    public function __construct(private readonly User $user)
    {}

    public function getUser(): User
    {
        return $this->user;
    }
}