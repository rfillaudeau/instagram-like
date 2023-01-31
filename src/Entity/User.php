<?php

namespace App\Entity;

use ApiPlatform\Metadata as ApiMethod;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Api\User\FollowUser;
use App\Controller\Api\User\GetByUsername;
use App\Controller\Api\User\GetCurrentUser;
use App\Controller\Api\User\UnfollowUser;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[ApiResource(
    operations: [
        new ApiMethod\Get(
            uriTemplate: '/users/me',
            controller: GetCurrentUser::class,
            openapi: new Operation(
                summary: 'Retrieves the current user.',
            ),
            security: 'is_granted("' . User::ROLE_USER . '")',
            read: false
        ),
        new ApiMethod\Get(
            uriTemplate: '/users/{id}',
            requirements: ['id' => '\d+'],
            name: 'api_get_user',
        ),
        new ApiMethod\Get(
            uriTemplate: '/users/username/{username}',
            controller: GetByUsername::class,
            openapi: new Operation(
                summary: 'Retrieves a User resource by the username.',
            ),
            read: false
        ),
        new ApiMethod\Delete(
            uriTemplate: '/users/{id}/follow',
            requirements: ['id' => '\d+'],
            controller: UnfollowUser::class,
            security: 'is_granted("' . User::ROLE_USER . '")',
            read: false,
        ),
    ],
    normalizationContext: [
        AbstractNormalizer::GROUPS => [
            User::GROUP_READ,
        ],
        AbstractObjectNormalizer::SKIP_NULL_VALUES => false
    ],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('username')]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const GROUP_READ = 'user:read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 30, unique: true)]
    #[Groups([self::GROUP_READ])]
    private ?string $username = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups([self::GROUP_READ])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var null|string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([self::GROUP_READ])]
    private ?string $bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Ignore]
    private ?string $avatarFilename = null;

    #[Groups([self::GROUP_READ])]
    private ?string $avatarFilePath = null;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private int $postCount = 0;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private int $followingCount = 0;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private int $followerCount = 0;

    #[Groups([self::GROUP_READ])]
    private bool $isFollowed = false;

    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_USER,
            self::ROLE_ADMIN,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    public function addRole(string $role): self
    {
        $roles = $this->getRoles();
        $roles[] = $role;

        $this->roles = array_unique($roles);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function removeRole(string $role): self
    {
        $roles = $this->getRoles();

        $roleKey = array_search($role, $roles, true);
        if ($roleKey === false) {
            return $this;
        }

        unset($roles[$roleKey]);

        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getAvatarFilename(): ?string
    {
        return $this->avatarFilename;
    }

    public function setAvatarFilename(?string $avatarFilename): self
    {
        $this->avatarFilename = $avatarFilename;
        return $this;
    }

    public function getAvatarFilePath(): ?string
    {
        return $this->avatarFilePath;
    }

    public function setAvatarFilePath(?string $avatarFilePath): self
    {
        $this->avatarFilePath = $avatarFilePath;
        return $this;
    }

    public function getPostCount(): int
    {
        return $this->postCount;
    }

    public function setPostCount(int $postCount): self
    {
        $this->postCount = $postCount;
        return $this;
    }

    public function getFollowingCount(): int
    {
        return $this->followingCount;
    }

    public function setFollowingCount(int $followingCount): self
    {
        $this->followingCount = $followingCount;
        return $this;
    }

    public function getFollowerCount(): int
    {
        return $this->followerCount;
    }

    public function setFollowerCount(int $followerCount): self
    {
        $this->followerCount = $followerCount;
        return $this;
    }

    public function isFollowed(): bool
    {
        return $this->isFollowed;
    }

    public function setIsFollowed(bool $isFollowed): self
    {
        $this->isFollowed = $isFollowed;
        return $this;
    }
}
