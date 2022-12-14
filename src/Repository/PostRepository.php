<?php

namespace App\Repository;

use App\Entity\Follow;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    /**
     * @param User $user
     * @param int $firstResult
     * @param int $maxResults
     * @return Post[]|array
     */
    public function findByFollowing(User $user, int $firstResult = 0, int $maxResults = 10): array
    {
        return $this->createQueryBuilder('p')
            ->distinct()
            ->leftJoin(Follow::class, 'f', Join::WITH, 'f.user = :user')
            ->andWhere('p.user = :user')
            ->orWhere('p.user = f.following')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $firstResult
     * @param int $maxResults
     * @return Post[]|array
     */
    public function findByLatest(int $firstResult = 0, int $maxResults = 10): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     * @param int $firstResult
     * @param int $maxResults
     * @return Post[]|array
     */
    public function findByUser(User $user, int $firstResult = 0, int $maxResults = 10): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

    public function incrementLikeCount(Post $post): void
    {
        $this->getEntityManager()
            ->createQuery(
                'UPDATE App\\Entity\\Post p SET p.likeCount = p.likeCount + 1 WHERE p.id = :id'
            )
            ->setParameter('id', $post->getId())
            ->execute();
    }

    public function decrementLikeCount(Post $post): void
    {
        $this->getEntityManager()
            ->createQuery(
                'UPDATE App\\Entity\\Post p SET p.likeCount = p.likeCount - 1 WHERE p.id = :id'
            )
            ->setParameter('id', $post->getId())
            ->execute();
    }
}
