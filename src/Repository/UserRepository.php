<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function incrementPostCount(User $user): void
    {
        $this->getEntityManager()->createQuery(sprintf(
            'UPDATE %s u SET u.postCount = u.postCount + 1 WHERE u.id = :id',
            User::class
        ))
            ->setParameter('id', $user->getId())
            ->execute();
    }

    public function decrementPostCount(User $user): void
    {
        $this->getEntityManager()->createQuery(sprintf(
            'UPDATE %s u SET u.postCount = u.postCount - 1 WHERE u.id = :id',
            User::class
        ))
            ->setParameter('id', $user->getId())
            ->execute();
    }

    public function incrementFollowerCount(User $user): void
    {
        $this->getEntityManager()->createQuery(sprintf(
            'UPDATE %s u SET u.followerCount = u.followerCount + 1 WHERE u.id = :id',
            User::class
        ))
            ->setParameter('id', $user->getId())
            ->execute();
    }

    public function decrementFollowerCount(User $user): void
    {
        $this->getEntityManager()->createQuery(sprintf(
            'UPDATE %s u SET u.followerCount = u.followerCount - 1 WHERE u.id = :id',
            User::class
        ))
            ->setParameter('id', $user->getId())
            ->execute();
    }

    public function incrementFollowingCount(User $user): void
    {
        $this->getEntityManager()->createQuery(sprintf(
            'UPDATE %s u SET u.followingCount = u.followingCount + 1 WHERE u.id = :id',
            User::class
        ))
            ->setParameter('id', $user->getId())
            ->execute();
    }

    public function decrementFollowingCount(User $user): void
    {
        $this->getEntityManager()->createQuery(sprintf(
            'UPDATE %s u SET u.followingCount = u.followingCount - 1 WHERE u.id = :id',
            User::class
        ))
            ->setParameter('id', $user->getId())
            ->execute();
    }
}
