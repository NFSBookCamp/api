<?php

namespace App\Service;

use App\Entity\Room;
use App\Repository\AccountRepository;
use App\Repository\DisciplineRepository;
use App\Repository\RoomRepository;

class RoomFactory
{
    public function __construct(
        private readonly RoomRepository $roomRepository,
        private readonly AccountRepository $accountRepository,
        private readonly DisciplineRepository $disciplineRepository
    )
    {
    }

    public function create(mixed $data): Room
    {
        $entity = (new Room())
            ->setNumber($data['number'])
            ->setStatus(Room::ROOM_STATUS_VACANT);

        if (!empty($data['caracteristics'])) {
            $entity->setCaracteristics($data['caracteristics']);
        }

        $this->roomRepository->save($entity, true);

        return $entity;
    }

    public function update(Room $entity, mixed $data): string|Room
    {
        if (!empty($data['caracteristics'])) {
            $entity->setCaracteristics($data['caracteristics']);
        }

        if (!empty($data['number'])) {
            $entity->setNumber($data['number']);
        }

        if (!empty($data['status'])) {
            $entity->setStatus($data['status']);

            if ($entity->getStatus() === Room::ROOM_STATUS_BOOKED) {
                $account = $this->accountRepository->find($data['accountId']);
                $entity->setBookedBy($account);
                $entity->setBookedAt(new \DateTime());

                if (!empty($data['disciplineId'])) {
                    $discipline = $this->disciplineRepository->find($data['disciplineId']);
                    $entity->setDiscipline($discipline);
                }

                if (empty($data['bookingDelay'])) {
                    if (!empty($data['disciplineId'])) {
                        $entity->setBookingDelay($discipline->getTime());
                    }
                } else {
                    $entity->setBookingDelay($data['bookingDelay']);
                }

                $places = $entity->getCaracteristics()['places'];

                if (!empty($data['participants'])) {
                    if(count($data['participants']) > $places) {
                        throw new \Exception("Il n'y pas assez de places dans la salle");
                    }

                    foreach ($data['participants'] as $participantId) {
                        $participant = $this->accountRepository->find($participantId);
                        $entity->addParticipant($participant);
                    }
                }
            } else {
                $entity->setBookedBy(null);
                $entity->setBookedAt(null);
                $entity->setDiscipline(null);
                $entity->setBookingDelay(null);
            }
        }

        $this->roomRepository->save($entity, true);

        return $entity;
    }
}