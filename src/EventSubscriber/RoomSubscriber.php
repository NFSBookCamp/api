<?php

namespace App\EventSubscriber;

use App\Entity\History;
use App\Entity\Room;
use App\Event\UpdateRoomEvent;
use App\Repository\HistoryRepository;

class RoomSubscriber
{
    public function __construct(
        private readonly HistoryRepository $historyRepository,
    )
    {}

    public function onUpdateRoom(UpdateRoomEvent $event): void
    {
        $room = $event->getRoom();

        if ($room->getStatus() === Room::ROOM_STATUS_BOOKED) {
            $history = (new History())
                ->setRoom($room)
                ->setDiscipline($room->getDiscipline())
                ->setBookedBy($room->getBookedBy())
                ->setRoomBookedAt($room->getBookedAt())
                ->setRoomBookingDelay($room->getBookingDelay());

            $this->historyRepository->save($history, true);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UpdateRoomEvent::NAME => 'onUpdateRoom',
        ];
    }
}