<?php

namespace App\Repository;

use App\Entity\Badge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Badge>
 */
class BadgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badge::class);
    }
    // src/Repository/BadgeRepository.php

    public function findBySearchQuery(string $searchQuery)
    {
        $qb = $this->createQueryBuilder('b');

        // Si une requête de recherche existe, on applique le filtre
        if ($searchQuery) {
            $qb->andWhere('b.name LIKE :searchQuery')
            ->orWhere('b.requiredScore LIKE :searchQuery')  // Filtrer par score requis
            
               ->setParameter('searchQuery', '%'.$searchQuery.'%');
        }

        return $qb->getQuery()->getResult(); // Exécution de la requête et récupération des résultats
    }



//    /**
//     * @return Badge[] Returns an array of Badge objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Badge
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
