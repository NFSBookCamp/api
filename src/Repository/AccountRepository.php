<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @extends ServiceEntityRepository<Account>
 *
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    use CommonRepositoryTrait;

    public function __construct(
        ManagerRegistry                                $registry,
        private readonly AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($registry, Account::class);
    }

    public function save(Account $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Account $entity, bool $flush = false): void
    {
        foreach ($entity->getRooms() as $room) {
            $room->setBookedBy(null);
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Account[] Returns an array of Discipline objects
     */
    public function findAllFilteredQuery(Request $request): array
    {
        $query = $this->createQueryBuilder('a');

        $data = $request->query->all();

        if (!empty($data)) {
            $this->filterRequestQuery($query, $data, 'a');
        }

        if(!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $query->andWhere('u.roles NOT LIKE :role')
                ->setParameter('role', '%ADMIN%');
        }

        return $query
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Account
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
