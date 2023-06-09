<?php

namespace App\Service;

use App\Entity\Discipline;
use App\Repository\DisciplineRepository;

class DisciplineFactory
{
    public function __construct(
        private readonly DisciplineRepository $disciplineRepository,
    )
    {
    }

    public function create(mixed $data): Discipline
    {
        $entity = (new Discipline())
            ->setName($data['name'])
            ->setTime($data['time']);

        if (!empty($data['description'])) {
            $entity->setDescription($data['description']);
        }

        $this->disciplineRepository->save($entity, true);

        return $entity;
    }

    public function update(Discipline $entity, mixed $data): string|Discipline
    {
        if (!empty($data['name'])) {
            $entity->setName($data['name']);
        }

        if (!empty($data['time'])) {
            $entity->setTime($data['time']);
        }

        if (!empty($data['description'])) {
            $entity->setTime($data['description']);
        }

        $this->disciplineRepository->save($entity, true);

        return $entity;
    }
}