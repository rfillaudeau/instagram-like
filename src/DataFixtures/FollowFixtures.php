<?php

namespace App\DataFixtures;

use App\Entity\Follow;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FollowFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $follow = (new Follow())
            ->setUser($this->getReference(UserFixtures::FOLLOWER_USER_REFERENCE))
            ->setFollowing($this->getReference(UserFixtures::FOLLOWED_USER_REFERENCE));

        $manager->persist($follow);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
