<?php

namespace App\Repository;

use App\Entity\Forum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Forum>
 */
class ForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forum::class);
    }

//    /**
//     * @return Forum[] Returns an array of Forum objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Forum
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function searchForums(string $query): array
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.nom LIKE :query OR f.theme LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery();
        return $qb->getResult();
    }

    public function findPaginatedForums(int $page, int $limit = 3)
    {
        return $this->createQueryBuilder('f')
            ->setFirstResult(($page - 1) * $limit) // OFFSET
            ->setMaxResults($limit) // LIMIT
            ->getQuery()
            ->getResult();
    }
}
