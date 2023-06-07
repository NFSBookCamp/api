<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public const SUPER_ADMIN = 'charlotte.saidi@outlook.fr';
    private $fakerFactory;

    public function __construct(private UserPasswordHasherInterface $passwordHasher) {
        $this->fakerFactory = \Faker\Factory::create('fr_FR');
    }

    public static function getGroups(): array
    {
        return ['user'];
    }

    public static function getSuperAdminReference(string $key): string
    {
        return User::class . '_SUPER_ADMIN_' . $key;
    }

    public static function getUserReference(string $key): string
    {
        return User::class . '_USER_' . $key;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getSuperAdminData() as $data) {
            $entity = $this->createUser($data);
            $manager->persist($entity);
            $this->addReference(self::getSuperAdminReference($entity->getEmail()), $entity);
        }

        $i = 0;
        foreach ($this->getData() as $data) {
            $entity = $this->createUser($data);
            $manager->persist($entity);
            $this->addReference(self::getUserReference((string) $i), $entity);
            ++$i;
        }

        $manager->flush();
    }

    private function createUser(array $data): User
    {
        $entity = new User();

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();

        if ($plainPassword = $data['plainPassword'] ?? null) {
            $password = $this->passwordHasher->hashPassword($entity, $plainPassword);
            $data['password'] = $password;
            unset($data['plainPassword']);
        }

        foreach ($data as $key => $value) {
            if ($propertyAccessor->isWritable($entity, $key)) {
                $propertyAccessor->setValue($entity, $key, $value);
            }
        }

        return $entity;
    }

    private function getSuperAdminData(): iterable
    {
        yield [
            'email' => self::SUPER_ADMIN,
            'plainPassword' => self::SUPER_ADMIN,
            'roles' => ['ROLE_SUPER_ADMIN']
        ];
    }

    private function getData(): iterable
    {
        $faker = $this->fakerFactory;

        for ($i = 0; $i < 100; ++$i) {
            $role = match ($i % 5) {
                0 => 'ROLE_ADMIN',
                3, 1, 2, 4 => 'ROLE_USER'
            };

            $email = $faker->email();

            $data = [
                'email' => $email,
                'plainPassword' => $email,
                'roles' => [$role]
            ];
            yield $data;
        }
    }
}