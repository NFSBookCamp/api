<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserFactory
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository              $userRepository,
        private readonly ValidatorInterface          $validator
    )
    {}

    public function create(mixed $data): User
    {
        $entity = (new User())
            ->setEmail($data['email']);
        if ($data['plainPassword'] !== $data['confirmPassword']) {
            throw new \Exception('Les mots de passe doivent correspondre');
        } else {
            $entity->setPassword($this->passwordHasher->hashPassword(
                $entity,
                $data['plainPassword']
            ));
        }

        if (!empty($data['roles'])) {
            $entity
                ->setRoles($data['roles']);
        }

        $this->validator->validate($entity);

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

        if (!empty($data['new_password'])) {
            if (!$this->passwordHasher->isPasswordValid($entity, $data['password'])) {
                throw new AccessDeniedHttpException('L\'ancien mot de passe est incorrect');
            } else {
                $entity->setPassword($this->passwordHasher->hashPassword(
                    $entity,
                    $data['new_password']
                ));
            }
        }

        $this->validator->validate($entity);

        $this->userRepository->save($entity, true);

        return $entity;
    }
}