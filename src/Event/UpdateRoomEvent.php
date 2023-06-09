<?php

namespace App\Event;

use App\Entity\Room;

class UpdateRoomEvent
{
    public const NAME = 'room.update';

    public function __construct(private readonly Room $room)
    {}

    public function getRoom(): Room
    {
        return $this->room;
    }
}