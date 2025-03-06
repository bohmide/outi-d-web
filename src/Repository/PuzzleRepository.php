<?php

namespace App\Repository;

use App\Entity\Puzzle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Puzzle>
 */
class PuzzleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Puzzle::class);
    }
    public function findBySearchQuery(string $searchQuery)
    {
        $qb = $this->createQueryBuilder('b');

        // Si une requête de recherche existe, on applique le filtre
        if ($searchQuery) {
            $qb->andWhere('b.id LIKE :searchQuery')
            
            
               ->setParameter('searchQuery', '%'.$searchQuery.'%');
        }

        return $qb->getQuery()->getResult(); // Exécution de la requête et récupération des résultats
    }

//    /**
//     * @return Puzzle[] Returns an array of Puzzle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Puzzle
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
