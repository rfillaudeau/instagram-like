<?php

namespace App\Tests\Unit\Serializer;

use App\Entity\Post;
use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PostNormalizerTest extends KernelTestCase
{
    /**
     * @throws Exception
     */
    public function testNormalizePost()
    {
        self::bootKernel();

        $container = static::getContainer();

        $user = UserNormalizerTest::makeUser();

        $post = (new Post())
            ->setDescription('Test description')
            ->setPictureFilename('my_picture.jpg')
            ->setLikeCount(15)
            ->setUser($user);

        $serializer = $container->get(SerializerInterface::class);
        $result = $serializer->normalize(
            $post,
            null,
            [AbstractNormalizer::GROUPS => [
                Post::GROUP_READ,
                User::GROUP_READ,
            ]]
        );

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('pictureFilePath', $result);
        $this->assertArrayHasKey('isLiked', $result);
        $this->assertArrayHasKey('likeCount', $result);
        $this->assertArrayHasKey('createdAt', $result);
        $this->assertArrayHasKey('updatedAt', $result);

        $this->assertArrayHasKey('id', $result['user']);
        $this->assertArrayHasKey('username', $result['user']);
        $this->assertArrayHasKey('email', $result['user']);
        $this->assertArrayHasKey('postCount', $result['user']);
        $this->assertArrayHasKey('followingCount', $result['user']);
        $this->assertArrayHasKey('followerCount', $result['user']);
        $this->assertArrayHasKey('isFollowed', $result['user']);
        $this->assertArrayHasKey('avatarFilePath', $result['user']);
        $this->assertArrayHasKey('bio', $result['user']);
        $this->assertArrayHasKey('id', $result['user']);

        $this->assertEquals('Test description', $result['description']);
        $this->assertEquals('/uploads/posts/my_picture.jpg', $result['pictureFilePath']);
        $this->assertEquals(false, $result['isLiked']);
        $this->assertEquals(15, $result['likeCount']);

        $this->assertEquals('test_username', $result['user']['username']);
        $this->assertEquals('test_email@test.com', $result['user']['email']);
        $this->assertEquals(25, $result['user']['postCount']);
        $this->assertEquals(70, $result['user']['followingCount']);
        $this->assertEquals(9, $result['user']['followerCount']);
        $this->assertEquals(false, $result['user']['isFollowed']);
        $this->assertEquals('/uploads/avatars/my_avatar.jpg', $result['user']['avatarFilePath']);
        $this->assertEquals('Hello there', $result['user']['bio']);
    }
}
