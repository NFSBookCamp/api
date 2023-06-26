<?php

namespace App\Service;

use App\Entity\Account;
use App\Repository\AccountRepository;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccountFactory
{
    public function __construct(
        private readonly AccountRepository  $accountRepository,
        private readonly UserRepository     $userRepository,
        private readonly ValidatorInterface $validator
    )
    {
    }

    public function create(mixed $data): Account
    {
        $user = $this->userRepository->find($data['userId']);

        $entity = (new Account())
            ->setStatus(Account::ACCOUNT_STATUS_PENDING)
            ->setEmail($user->getEmail())
            ->setUser($user)
            ->setType($data["type"]);

        if (!empty($data['firstname'])) {
            $entity->setFirstname($data['firstname']);
        }

        if (!empty($data['lastname'])) {
            $entity->setLastname($data['lastname']);
        }

        if (!empty($data['address'])) {
            $entity->setAddress($data['address']);
        }

        if (!empty($data['phone'])) {
            $entity->setPhone($data['phone']);
        }

        $this->validator->validate($entity);

        $user->setAccount($entity);
        $this->accountRepository->save($entity, true);

        return $entity;
    }

    public function update(Account $entity, mixed $data): string|Account
    {
        $user = $entity->getUser();

        if (!empty($data['email'])) {
            $entity->setEmail($data['email']);
            $user->setEmail($data['email']);
        }

        if (!empty($data['firstname'])) {
            $entity->setFirstname($data['firstname']);
        }

        if (!empty($data['lastname'])) {
            $entity->setLastname($data['lastname']);
        }

        if (!empty($data['address'])) {
            $entity->setAddress($data['address']);
        }

        if (!empty($data['phone'])) {
            $entity->setPhone($data['phone']);
        }

        if (!empty($data['type'])) {
            $entity->setType($data['type']);
            switch ($entity->getType()) {
                case Account::ACCOUNT_TYPE_STUDENT:
                    $user->setRoles(["ROLE_USER"]);
                    break;
                case Account::ACCOUNT_TYPE_ADMIN:
                    $user->setRoles(["ROLE_ADMIN"]);
                    break;
                case Account::ACCOUNT_TYPE_SUPER_ADMIN:
                    $user->setRoles(["ROLE_SUPER_ADMIN"]);
                    break;
                default:
                    $user->setRoles(["ROLE_USER"]);
            }
        }

        if (!empty($data['status'])) {
            $entity->setType($data['status']);
        }

        $this->validator->validate($entity);

        $this->accountRepository->save($entity, true);

        return $entity;
    }
}