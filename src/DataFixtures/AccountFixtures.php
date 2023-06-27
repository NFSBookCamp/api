<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class AccountFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private $fakerFactory;

    public function __construct() {
        $this->fakerFactory = \Faker\Factory::create('fr_FR');
    }

    public static function getGroups(): array
    {
        return ['account'];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public static function getAccountSuperAdminReference(string $key): string
    {
        return Account::class . '_SUPER_ADMIN_' . $key;
    }

    public static function getAccountTeacherReference(string $key): string
    {
        return Account::class . '_TEACHER_' . $key;
    }

    public static function getAccountStudentReference(string $key): string
    {
        return Account::class . '_STUDENT_' . $key;
    }

    public static function getAccountAdminReference(string $key): string
    {
        return Account::class . '_ADMIN_' . $key;
    }
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getSuperAdminAccountData() as $data) {
            $entity = $this->createAccount($data);
            $manager->persist($entity);
            /** @var User $user */
            $user = $this->getReference(UserFixtures::getSuperAdminReference($entity->getEmail()));
            $user->setAccount($entity);
            $entity->setEmail($user->getEmail());
            $this->addReference(self::getAccountSuperAdminReference($entity->getEmail()), $entity);
        }

        $iTeacher = 0;
        $iAdmin = 0;
        $iStudent = 0;
        $i = 0;
        foreach ($this->getData() as $data) {
            $entity = $this->createAccount($data);
            $manager->persist($entity);
            /** @var User $user */
            $user = $this->getReference(UserFixtures::getUserReference((string) $data['user_id']));
            $user->setAccount($entity);
            $entity->setEmail($user->getEmail());
            switch ($entity->getType()) {
                case Account::ACCOUNT_TYPE_TEACHER:
                    $this->addReference(self::getAccountTeacherReference((string) $iTeacher), $entity);
                    ++$iTeacher;
                    break;
                case Account::ACCOUNT_TYPE_ADMIN:
                    $this->addReference(self::getAccountAdminReference((string) $iAdmin), $entity);
                    ++$iAdmin;
                    break;
                case Account::ACCOUNT_TYPE_STUDENT:
                    $this->addReference(self::getAccountStudentReference((string) $iStudent), $entity);
                    ++$iStudent;
                    break;
            }
            if ($entity->getStatus() === Account::ACCOUNT_STATUS_ACTIVE) {
                $user->setEnabled(true);
            }
            ++$i;
        }

        $manager->flush();
    }

    private function createAccount(array $data): Account
    {
        $entity = new Account();
        // Default
        $entity->setStatus(Account::ACCOUNT_STATUS_ACTIVE);
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

    private function getSuperAdminAccountData(): iterable
    {
        yield [
            'type' => Account::ACCOUNT_TYPE_SUPER_ADMIN,
            'firstname' => 'Charlotte',
            'lastname' => 'Saidi',
            'address' => '707 route de brotonne, 27520 Grand-Bourgtheroulde',
            'phone' => '0638758904',
            'status' => Account::ACCOUNT_STATUS_ACTIVE,
            'email' => UserFixtures::SUPER_ADMIN
        ];
    }

    private function getData(): iterable
    {
        for ($i = 0; $i < 100; ++$i) {
            yield match ($i % 5) {
                0 => $this->getAdminAccountData($i, Account::ACCOUNT_STATUS_ACTIVE),
                3 => $this->getStudentAccountData($i, Account::ACCOUNT_STATUS_ACTIVE),
                1, 2, 4 => $this->getTeacherAccountData($i, Account::ACCOUNT_STATUS_ACTIVE)
            };
        }

        for ($i = 100; $i < 150; ++$i) {
            yield match ($i % 5) {
                0 => $this->getAdminAccountData($i, Account::ACCOUNT_STATUS_PENDING),
                3 => $this->getStudentAccountData($i, Account::ACCOUNT_STATUS_DISABLED),
                1, 2, 4 => $this->getTeacherAccountData($i, Account::ACCOUNT_STATUS_PENDING)
            };
        }
    }

    private function getAdminAccountData(int $i, string $status): array
    {
        $faker = $this->fakerFactory;

        return [
            'user_id' => $i,
            'type' => Account::ACCOUNT_TYPE_ADMIN,
            'firstname' => $faker->firstName(),
            'lastname' => $faker->lastName(),
            'address' => $faker->address(),
            'phone' => $faker->phoneNumber(),
            'status' => $status
        ];
    }

    private function getTeacherAccountData(int $i, string $status): array
    {
        $faker = $this->fakerFactory;

        return [
            'user_id' => $i,
            'type' => Account::ACCOUNT_TYPE_TEACHER,
            'firstname' => $faker->firstName(),
            'lastname' => $faker->lastName(),
            'address' => $faker->address(),
            'phone' => $faker->phoneNumber(),
            'status' => $status
        ];
    }

    private function getStudentAccountData(int $i, string $status): array
    {
        $faker = $this->fakerFactory;

        return [
            'user_id' => $i,
            'type' => Account::ACCOUNT_TYPE_STUDENT,
            'firstname' => $faker->firstName(),
            'lastname' => $faker->lastName(),
            'address' => $faker->address(),
            'phone' => $faker->phoneNumber(),
            'status' => $status
        ];
    }
}