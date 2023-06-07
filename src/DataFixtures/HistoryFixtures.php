<?php

namespace App\DataFixtures;

use App\Entity\History;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class HistoryFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['history'];
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            DisciplineFixtures::class,
            RoomFixtures::class
        ];
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 9; $i++) {
            $room = $this->getReference(RoomFixtures::getRoomReference((string) $i));
            $data = [
                'room' => $room,
                'bookedBy' => $room->getBookedBy(),
                'discipline' => $room->getDiscipline(),
                'roomBookingDelay' => $room->getDiscipline()->getTime(),
                'roomBookedAt' => $room->getBookedAt()
            ];
            $entity = $this->createHistory($data);
            $manager->persist($entity);
            ++$i;
        }
        $manager->flush();
    }

    private function createHistory(array $data): History
    {
        $entity = new History();
        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();
        foreach ($data as $key => $value) {
            if ($propertyAccessor->isWritable($entity, $key)) {
                $propertyAccessor->setValue($entity, $key, $value);
            }
        }

        return $entity;
    }

//    private function getData(): iterable
//    {
//        for ($i = 0; $i < 9; $i++) {
//            $room = $this->getReference(RoomFixtures::getRoomReference($i));
//
//            yield [
//                'room' => $room,
//                'bookedBy' => $room->getBookedBy(),
//                'discipline' => $room->getDiscipline(),
//                'roomBookingDelay' => $room->getDiscipline()->getTime(),
//                'roomBookedAt' => $room->getBookedAt()
//            ];
//        }
//    }
}