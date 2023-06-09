<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository              $userRepository
    )
    {}

    public function create(mixed $data): User
    {
        $entity = (new User())
            ->setEmail($data['email']);
        $entity
            ->setPassword($this->passwordHasher->hashPassword(
                $entity,
                $data['plainPassword']
            ));

        if (!empty($data['roles'])) {
            $entity
                ->setRoles($data['roles']);
        }

        $this->userRepository->save($entity, true);

        return $entity;
    }

    public function update(User $entity, mixed $data): string|User
    {
        if (!empty($data['email'])) {
            $entity->setEmail($data['email']);
        }

        if (!empty($data['roles'])) {
            $entity->setRoles($data['roles']);
        }

        if (!empty($data['plainPassword'])) {
            if ($data['plainPassword'] !== $data['confirmPassword']) {
                throw new \Exception('Les mots de passe doivent correspondre');
            } else {
                $entity->setPassword($this->passwordHasher->hashPassword(
                    $entity,
                    $data['plainPassword']
                ));
            }
        }

        $this->userRepository->save($entity, true);

        return $entity;
    }
}