<?php

namespace App\Repository;

use App\Entity\Forum;
use App\Entity\Post;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    //    /**
    //     * @return Post[] Returns an array of Post objects
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

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findPostsByLikes(Forum $forum)
    {
        return $this->createQueryBuilder('p')
            ->where('p.forum = :forum') // Ensure you reference the forum field correctly
            ->setParameter('forum', $forum)
            ->orderBy('p.nb_like', 'DESC') // Sort by number of likes in descending order
            ->getQuery()
            ->getResult();
    }
    public function findPaginatedPosts(Forum $forum, int $page, int $limit = 3)
    {
        return $this->createQueryBuilder('p')
            ->where('p.forum = :forum')
            ->setParameter('forum', $forum)
            ->orderBy('p.nb_like', 'DESC') // Ensure this matches the actual entity field name
            ->setFirstResult(($page - 1) * $limit) // OFFSET
            ->setMaxResults($limit) // LIMIT
            ->getQuery()
            ->getResult();
    }
}