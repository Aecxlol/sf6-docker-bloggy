<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Query
     */
    public function getAllPublishedArticlesQuery(): Query
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
        ;
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param string $slug
     * @return Post|null
     * @throws NonUniqueResultException
     */
    public function findOneByPublishedDateAndSlug(int $year, int $month, int $day, string $slug): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('YEAR(p.publishedAt) = :year')
            ->andWhere('MONTH(p.publishedAt) = :month')
            ->andWhere('DAY(p.publishedAt) = :day')
            ->andWhere('p.slug = :slug')
            ->setParameters([
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'slug' => $slug
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
