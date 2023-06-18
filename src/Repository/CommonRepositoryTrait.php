<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

trait CommonRepositoryTrait
{
    public function filterRequestQuery(QueryBuilder $query, array $request, string $alias): void
    {
        foreach ($request as $key => $value) {
            if ($key === 'range') {
                $query
                    ->setFirstResult(json_decode($value)[0])
                    ->setMaxResults(json_decode($value)[1]);
            }

            if ($key === 'sort') {
                $query
                    ->orderBy($alias . '.' . json_decode($value)[0], json_decode($value)[1]);
            }

            if ($key !== 'range' && $key !== 'sort') {
                $query->andWhere($alias . '.' . $key . ' LIKE :key')
                    ->setParameters(['key' => '%' . $value . '%']);
            }
        }
    }
}