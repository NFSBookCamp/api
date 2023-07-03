<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class RoomFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private $fakerFactory;

    public function __construct() {
        $this->fakerFactory = \Faker\Factory::create('fr_FR');
    }

    public static function getGroups(): array
    {
        return ['room'];
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            DisciplineFixtures::class
        ];
    }

    public static function getRoomReference(string $key): string
    {
        return Room::class . '_ROOM_' . $key;
    }

    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach ($this->getData() as $data) {
            $entity = $this->createRoom($data);
            $manager->persist($entity);
            $this->addReference(self::getRoomReference((string) $i), $entity);

            if ($entity->getStatus() === Room::ROOM_STATUS_BOOKED) {
                $teacher = $this->getReference(AccountFixtures::getAccountTeacherReference($i));
                $student = $this->getReference(AccountFixtures::getAccountStudentReference($i));
                $discipline = $this->getReference(DisciplineFixtures::getDisciplineReference($i));
                $entity->setBookedBy($teacher);
                $entity->setDiscipline($discipline);
                $entity->setBookingDelay($discipline->getTime());
                $entity->addParticipant($student);
                $entity->setReserved(true);
            }

            ++$i;
        }
        $manager->flush();
    }

    private function createRoom(array $data): Room
    {
        $entity = new Room();
        // Data
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

    private function getData(): iterable
    {
        for ($i = 0; $i < 20; $i++) {
            yield match ($i < 9) {
                true => $this->getBookedRoomData(),
                false => $this->getVacantRoomData()
            };
        }
    }

    private function getBookedRoomData(): array
    {
        $faker = $this->fakerFactory;

        return [
            'number' => strtoupper($faker->randomLetter()).((string)$faker->numberBetween(1, 2)).'0'. ((string)$faker->numberBetween(0, 9)),
            'caracteristics' => [
                'places' => $faker->numberBetween(15, 30),
                'materiel' => [
                    'screen' => $faker->numberBetween(0, 2),
                    'plug' => $faker->numberBetween(4, 12),
                    'remote' => $faker->numberBetween(0, 2),
                    'camera' => $faker->numberBetween(0, 2)
                ]
            ],
            'status' => Room::ROOM_STATUS_BOOKED,
            'bookedAt' => $faker->dateTimeBetween('-15 days', 'now'),
            'bookedOn' => $faker->dateTimeBetween('now', '+15 days')
        ];
    }

    private function getVacantRoomData(): array
    {
        $faker = $this->fakerFactory;

        return [
            'number' => strtoupper($faker->randomLetter()).((string)$faker->numberBetween(1, 2)).'0'. ((string)$faker->numberBetween(0, 9)),
            'caracteristics' => [
                'places' => $faker->numberBetween(15, 30),
                'materiel' => [
                    'screen' => $faker->numberBetween(0, 2),
                    'plug' => $faker->numberBetween(4, 12),
                    'remote' => $faker->numberBetween(0, 2),
                    'camera' => $faker->numberBetween(0, 2)
                ]
            ],
            'status' => Room::ROOM_STATUS_VACANT
        ];
    }
}
