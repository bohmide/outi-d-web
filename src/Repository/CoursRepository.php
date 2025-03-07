<?php

namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cours>
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    //    /**
    //     * @return Cours[] Returns an array of Cours objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Cours
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function searchCours(array $criteria): array
    {
        $qb = $this->createQueryBuilder('c');

        if (!empty($criteria['nom'])) {
            $qb->andWhere('c.nom LIKE :nom')
               ->setParameter('nom', '%' . $criteria['nom'] . '%');
        }

        if (!empty($criteria['etat'])) {
            $qb->andWhere('c.etat = :etat')
               ->setParameter('etat', $criteria['etat']);
        }

        if (!empty($criteria['dateCreation'])) {
            $qb->andWhere('c.date_creation = :dateCreation')
               ->setParameter('dateCreation', new \DateTime($criteria['dateCreation']));
        }

        return $qb->getQuery()->getResult();
    }
}
