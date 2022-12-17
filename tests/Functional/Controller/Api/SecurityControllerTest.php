<?php

namespace App\Tests\Functional\Controller\Api;

use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testICanLogin()
    {
        $client = static::createClient();

        $client->jsonRequest('POST', '/api/login', [
            'email' => 'test_1@test.com',
            'password' => 'qwerty',
        ]);

        $this->assertResponseIsSuccessful();

        $jsonResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $jsonResponse);
        $this->assertArrayHasKey('username', $jsonResponse);
        $this->assertArrayHasKey('email', $jsonResponse);
        $this->assertArrayHasKey('avatarFilename', $jsonResponse);
        $this->assertArrayHasKey('postCount', $jsonResponse);
        $this->assertArrayHasKey('followingCount', $jsonResponse);
        $this->assertArrayHasKey('followerCount', $jsonResponse);
        $this->assertArrayHasKey('isFollowed', $jsonResponse);
        $this->assertArrayHasKey('avatarFilepath', $jsonResponse);
        $this->assertArrayHasKey('bio', $jsonResponse);
        $this->assertArrayHasKey('id', $jsonResponse);

        $this->assertEquals('test_1', $jsonResponse['username']);
        $this->assertEquals('test_1@test.com', $jsonResponse['email']);
        $this->assertEquals(null, $jsonResponse['avatarFilename']);
        $this->assertEquals(0, $jsonResponse['postCount']);
        $this->assertEquals(0, $jsonResponse['followingCount']);
        $this->assertEquals(0, $jsonResponse['followerCount']);
        $this->assertEquals(false, $jsonResponse['isFollowed']);
        $this->assertEquals('/default_avatar.jpg', $jsonResponse['avatarFilepath']);
        $this->assertEquals(null, $jsonResponse['bio']);
    }

    public function testICanRegister()
    {
        $client = static::createClient();

        $client->jsonRequest('POST', '/api/register', [
            'email' => 'test_register@test.com',
            'username' => 'test_register',
            'plainPassword' => 'qwerty',
        ]);

        $this->assertResponseIsSuccessful();

        $jsonResponse = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $jsonResponse);
        $this->assertArrayHasKey('username', $jsonResponse);
        $this->assertArrayHasKey('email', $jsonResponse);
        $this->assertArrayHasKey('avatarFilename', $jsonResponse);
        $this->assertArrayHasKey('postCount', $jsonResponse);
        $this->assertArrayHasKey('followingCount', $jsonResponse);
        $this->assertArrayHasKey('followerCount', $jsonResponse);
        $this->assertArrayHasKey('isFollowed', $jsonResponse);
        $this->assertArrayHasKey('avatarFilepath', $jsonResponse);
        $this->assertArrayHasKey('bio', $jsonResponse);
        $this->assertArrayHasKey('id', $jsonResponse);

        $this->assertEquals('test_register', $jsonResponse['username']);
        $this->assertEquals('test_register@test.com', $jsonResponse['email']);
        $this->assertEquals(null, $jsonResponse['avatarFilename']);
        $this->assertEquals(0, $jsonResponse['postCount']);
        $this->assertEquals(0, $jsonResponse['followingCount']);
        $this->assertEquals(0, $jsonResponse['followerCount']);
        $this->assertEquals(false, $jsonResponse['isFollowed']);
        $this->assertEquals('/default_avatar.jpg', $jsonResponse['avatarFilepath']);
        $this->assertEquals(null, $jsonResponse['bio']);
    }

    /**
     * @throws Exception
     */
    public function testICanLogout()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('test_1@test.com');

        $client->loginUser($testUser);

        $client->request('GET', '/logout');

        $this->assertResponseIsSuccessful();
    }
}
