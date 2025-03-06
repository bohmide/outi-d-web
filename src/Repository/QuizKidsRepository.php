<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\QuizKids;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuizKids>
 */
class QuizKidsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizKids::class);
    }
    
    public function countQuestions(): int
    {
        return $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findByGenreAndLevel(?string $level, ?string $genre)
    {
        $qb = $this->createQueryBuilder('q');
    
        // Apply level filter only if it's provided
        if (!empty($level)) {
            $qb->andWhere('q.level = :level')
               ->setParameter('level', $level);
        }
    
        // Apply genre filter only if it's provided
        if (!empty($genre)) {
            $qb->andWhere('q.genre = :genre')
               ->setParameter('genre', $genre);
        }
    
        return $qb->getQuery()->getResult();
    }
    public function findBySearchQuery(string $searchQuery)
    {
        $qb = $this->createQueryBuilder('b');

        // Si une requête de recherche existe, on applique le filtre
        if ($searchQuery) {
            $qb->andWhere('b.question LIKE :searchQuery')
            ->orWhere('b.options LIKE :searchQuery')  // Filtrer par score requis
            ->orWhere('b.correctAnswer LIKE :searchQuery')           // Filtrer par icône (si nécessaire)
        
            ->orWhere('b.level LIKE :searchQuery')  // Filtrer par score requis
            ->orWhere('b.score LIKE :searchQuery')           // Filtrer par icône (si nécessaire)
            ->orWhere('b.genre LIKE :searchQuery')  // Filtrer par score requis
            ->orWhere('b.country LIKE :searchQuery')  // Filtrer par score requis
            
          
               ->setParameter('searchQuery', '%'.$searchQuery.'%');
        }

        return $qb->getQuery()->getResult(); // Exécution de la requête et récupération des résultats
    }


//    /**
//     * @return Quiz[] Returns an array of Quiz objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Quiz
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
