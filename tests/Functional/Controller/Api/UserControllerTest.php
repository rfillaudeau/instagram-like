<?php

namespace App\Tests\Functional\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testICanGetUserInfo()
    {
        $client = static::createClient();

        $client->jsonRequest('GET', '/api/users/test_1');

        $this->assertResponseIsSuccessful();
    }

    public function testICannotGetUserInfoFromNonExistingUser()
    {
        $client = static::createClient();

        $client->jsonRequest('GET', '/api/users/test_not_found');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testICannotFollowIfIAmNotLoggedIn()
    {
        $client = static::createClient();

        $client->jsonRequest('POST', '/api/users/test_to_follow/follow');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testICanFollowExistingUser()
    {
        $client = static::createClient();

        $client->loginUser($this->getUser('test_follower@test.com'));

        $client->jsonRequest('POST', '/api/users/test_to_follow/follow');

        $this->assertResponseStatusCodeSame(201);

        $client->jsonRequest('POST', '/api/users/test_to_follow/follow');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testICannotFollowMyself()
    {
        $client = static::createClient();

        $client->loginUser($this->getUser('test_follower@test.com'));

        $client->jsonRequest('POST', '/api/users/test_follower/follow');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testICannotFollowNonExistingUser()
    {
        $client = static::createClient();

        $client->loginUser($this->getUser('test_follower@test.com'));

        $client->jsonRequest('POST', '/api/users/test_not_found/follow');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testICannotUnfollowIfIAmNotLoggedIn()
    {
        $client = static::createClient();

        $client->jsonRequest('DELETE', '/api/users/test_to_follow/follow');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testICanUnfollowExistingUser()
    {
        $client = static::createClient();

        $client->loginUser($this->getUser('test_follower@test.com'));

        $client->jsonRequest('DELETE', '/api/users/test_followed/follow');

        $this->assertResponseStatusCodeSame(200);

        $client->jsonRequest('DELETE', '/api/users/test_followed/follow');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testICannotUnfollowNonExistingUser()
    {
        $client = static::createClient();

        $client->loginUser($this->getUser('test_follower@test.com'));

        $client->jsonRequest('DELETE', '/api/users/test_not_found/follow');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testICannotUnfollowMyself()
    {
        $client = static::createClient();

        $client->loginUser($this->getUser('test_follower@test.com'));

        $client->jsonRequest('DELETE', '/api/users/test_follower/follow');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testICannotUnfollowUserIDoNotFollow()
    {
        $client = static::createClient();

        $client->loginUser($this->getUser('test_follower@test.com'));

        $client->jsonRequest('DELETE', '/api/users/test_1/follow');

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @throws Exception
     */
    private function getUser(string $email):User
    {
        return static::getContainer()
            ->get(UserRepository::class)
            ->findOneByEmail($email);
    }
}
