<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const FOLLOWER_USER_REFERENCE = 'follower-user';
    public const FOLLOWED_USER_REFERENCE = 'followed-user';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {}

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->getUserToFollow());
        $manager->persist($this->getUserFollower());
        $manager->persist($this->getUserFollowed());

        for ($i = 1; $i <= 5; $i++) {
            $user = (new User())
                ->setUsername('test_' . $i)
                ->setEmail('test_' . $i . '@test.com');

            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                'qwerty'
            ));

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getUserFollower(): User
    {
        $user = (new User())
            ->setUsername('test_follower')
            ->setEmail('test_follower@test.com');

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'qwerty'
        ));

        $this->addReference(self::FOLLOWER_USER_REFERENCE, $user);

        return $user;
    }

    public function getUserToFollow(): User
    {
        $user = (new User())
            ->setUsername('test_to_follow')
            ->setEmail('test_to_follow@test.com');

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'qwerty'
        ));

        return $user;
    }

    public function getUserFollowed(): User
    {
        $user = (new User())
            ->setUsername('test_followed')
            ->setEmail('test_followed@test.com');

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'qwerty'
        ));

        $this->addReference(self::FOLLOWED_USER_REFERENCE, $user);

        return $user;
    }
}
