<?php

namespace App\Repository;

use App\Entity\Discipline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Discipline>
 *
 * @method Discipline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discipline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discipline[]    findAll()
 * @method Discipline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discipline::class);
    }

    public function save(Discipline $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Discipline $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Discipline[] Returns an array of Discipline objects
     */
    public function findAllFilteredQuery(Request $request): array
    {
        $query = $this->createQueryBuilder('d');

        $filters = $request->query->all();

        if (!empty($filters)) {
            foreach ($filters as $property => $value) {
                $query->andWhere('d.' . $property . ' LIKE :property')
                    ->setParameters(['property' => '%' . $value . '%']);
            }
        }

        return $query
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Discipline
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
