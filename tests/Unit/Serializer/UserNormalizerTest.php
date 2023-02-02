<?php

namespace App\Tests\Unit\Serializer;

use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserNormalizerTest extends KernelTestCase
{
    /**
     * @throws Exception
     */
    public function testNormalizeUser()
    {
        self::bootKernel();

        $container = static::getContainer();

        $user = self::makeUser();

        $serializer = $container->get(SerializerInterface::class);
        $result = $serializer->normalize(
            $user,
            null,
            [AbstractNormalizer::GROUPS => [
                User::GROUP_READ,
            ]]
        );

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('postCount', $result);
        $this->assertArrayHasKey('followingCount', $result);
        $this->assertArrayHasKey('followerCount', $result);
        $this->assertArrayHasKey('isFollowed', $result);
        $this->assertArrayHasKey('avatarFilePath', $result);
        $this->assertArrayHasKey('bio', $result);

        $this->assertEquals('test_username', $result['username']);
        $this->assertEquals('test_email@test.com', $result['email']);
        $this->assertEquals(25, $result['postCount']);
        $this->assertEquals(70, $result['followingCount']);
        $this->assertEquals(9, $result['followerCount']);
        $this->assertEquals(false, $result['isFollowed']);
        $this->assertEquals('/uploads/avatars/my_avatar.jpg', $result['avatarFilePath']);
        $this->assertEquals('Hello there', $result['bio']);
    }

    public static function makeUser(): User
    {
        return (new User())
            ->setUsername('test_username')
            ->setEmail('test_email@test.com')
            ->setBio('Hello there')
            ->setAvatarFilename('my_avatar.jpg')
            ->setPostCount(25)
            ->setFollowerCount(9)
            ->setFollowingCount(70);
    }

    /**
     * @throws Exception
     */
    public function testNormalizeUserWithoutAvatar()
    {
        self::bootKernel();

        $container = static::getContainer();

        $user = self::makeUser()
            ->setAvatarFilename(null);

        $serializer = $container->get(SerializerInterface::class);
        $result = $serializer->normalize(
            $user,
            null,
            [AbstractNormalizer::GROUPS => [
                User::GROUP_READ,
            ]]
        );

        $this->assertEquals('/default_avatar.jpg', $result['avatarFilePath']);
    }
}
