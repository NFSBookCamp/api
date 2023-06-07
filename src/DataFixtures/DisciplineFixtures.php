<?php

namespace App\DataFixtures;

use App\Entity\Discipline;
use App\Entity\Room;
use \Doctrine\Bundle\FixturesBundle\Fixture;
use \Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DisciplineFixtures extends Fixture implements FixtureGroupInterface
{

    private $fakerFactory;

    public function __construct() {
        $this->fakerFactory = \Faker\Factory::create('fr_FR');
    }

    public static function getGroups(): array
    {
        return ['discipline'];
    }

    public static function getDisciplineReference(string $key): string
    {
        return Room::class . '_DISCIPLINE_' . $key;
    }

    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach ($this->getData() as $data) {
            $entity = $this->createDiscipline($data);
            $manager->persist($entity);
            $this->addReference(self::getDisciplineReference((string) $i), $entity);
            ++$i;
        }
        $manager->flush();
    }

    private function createDiscipline(array $data): Discipline
    {
        $entity = new Discipline();
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
        $faker = $this->fakerFactory;

        for ($i = 0; $i < 9; $i++) {
            yield [
                'name' => self::getDisciplineNames()[$i],
                'description' => '',
                'time' => $faker->numberBetween(1, 7),
            ];
        }
    }

    private static function getDisciplineNames(): array
    {
        return [
            'UX/UI',
            'Symfony',
            'Algorithmie',
            'Audiovisuel',
            'Management',
            'Droit Informatique',
            'Framework JS',
            'HTML/CSS',
            'Cloud et Infrastructure',
            'Gestion de projet',
        ];
    }
}
