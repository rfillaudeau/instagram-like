<?php

namespace App\Tests\Unit\Serializer;

use App\Entity\Post;
use App\Entity\User;
use DateTime;
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

        $createdAt = new DateTime('2022-12-13 16:17:18');
        $updatedAt = new DateTime('2022-12-22 18:19:20');

        $user = UserNormalizerTest::makeUser();

        $post = (new Post())
            ->setDescription('Test description')
            ->setPictureFilename('my_picture.jpg')
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt)
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

        $this->assertEquals([
            'id' => null,
            'description' => 'Test description',
            'pictureFilename' => 'my_picture.jpg',
            'pictureFilepath' => '/uploads/posts/my_picture.jpg',
            'isLiked' => false,
            'createdAt' => '2022-12-13T16:17:18+00:00',
            'updatedAt' => '2022-12-22T18:19:20+00:00',
            'user' => [
                'id' => null,
                'username' => 'test_username',
                'email' => 'test_email@test.com',
                'avatarFilename' => 'my_avatar.jpg',
                'postCount' => 25,
                'followingCount' => 70,
                'followerCount' => 9,
                'isFollowed' => false,
                'avatarFilepath' => '/uploads/avatars/my_avatar.jpg',
                'bio' => 'Hello there',
            ],
            'likeCount' => 15
        ], $result);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('pictureFilename', $result);
        $this->assertArrayHasKey('pictureFilepath', $result);
        $this->assertArrayHasKey('isLiked', $result);
        $this->assertArrayHasKey('likeCount', $result);
        $this->assertArrayHasKey('createdAt', $result);
        $this->assertArrayHasKey('updatedAt', $result);

        $this->assertArrayHasKey('id', $result['user']);
        $this->assertArrayHasKey('username', $result['user']);
        $this->assertArrayHasKey('email', $result['user']);
        $this->assertArrayHasKey('avatarFilename', $result['user']);
        $this->assertArrayHasKey('postCount', $result['user']);
        $this->assertArrayHasKey('followingCount', $result['user']);
        $this->assertArrayHasKey('followerCount', $result['user']);
        $this->assertArrayHasKey('isFollowed', $result['user']);
        $this->assertArrayHasKey('avatarFilepath', $result['user']);
        $this->assertArrayHasKey('bio', $result['user']);
        $this->assertArrayHasKey('id', $result['user']);

        $this->assertEquals('Test description', $result['description']);
        $this->assertEquals('my_picture.jpg', $result['pictureFilename']);
        $this->assertEquals('/uploads/posts/my_picture.jpg', $result['pictureFilepath']);
        $this->assertEquals(false, $result['isLiked']);
        $this->assertEquals('2022-12-13T16:17:18+00:00', $result['createdAt']);
        $this->assertEquals('2022-12-22T18:19:20+00:00', $result['updatedAt']);
        $this->assertEquals(15, $result['likeCount']);

        $this->assertEquals('test_username', $result['user']['username']);
        $this->assertEquals('test_email@test.com', $result['user']['email']);
        $this->assertEquals('my_avatar.jpg', $result['user']['avatarFilename']);
        $this->assertEquals(25, $result['user']['postCount']);
        $this->assertEquals(70, $result['user']['followingCount']);
        $this->assertEquals(9, $result['user']['followerCount']);
        $this->assertEquals(false, $result['user']['isFollowed']);
        $this->assertEquals('/uploads/avatars/my_avatar.jpg', $result['user']['avatarFilepath']);
        $this->assertEquals('Hello there', $result['user']['bio']);
    }
}
