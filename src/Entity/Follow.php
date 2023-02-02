<?php

namespace App\Entity;

use ApiPlatform\Metadata as ApiMethod;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use App\Controller\Api\Follow\CreateFollow;
use App\Controller\Api\Follow\DeleteFollow;
use App\Repository\FollowRepository;
use App\Validator as AppAssert;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new ApiMethod\GetCollection(
            uriTemplate: '/users/{id}/followers',
            uriVariables: [
                'id' => new Link(
                    toProperty: 'following',
                    fromClass: User::class
                )
            ],
        ),
        new ApiMethod\GetCollection(
            uriTemplate: '/users/{id}/following',
            uriVariables: [
                'id' => new Link(
                    toProperty: 'user',
                    fromClass: User::class
                )
            ],
        ),
        new ApiMethod\Post(
            uriTemplate: '/users/{id}/follow',
            controller: CreateFollow::class,
            security: 'is_granted("' . User::ROLE_USER . '")',
            read: false,
        ),
        new ApiMethod\Delete(
            uriTemplate: '/users/{id}/follow',
            controller: DeleteFollow::class,
            security: 'is_granted("' . User::ROLE_USER . '")',
            read: false,
        ),
    ],
    normalizationContext: [
        AbstractNormalizer::GROUPS => [
            self::GROUP_READ,
            User::GROUP_READ,
        ],
        AbstractObjectNormalizer::SKIP_NULL_VALUES => false
    ],
    denormalizationContext: [
        AbstractNormalizer::GROUPS => [
            self::GROUP_WRITE,
        ],
    ],
    validationContext: [
        AbstractNormalizer::GROUPS => [
            self::GROUP_WRITE,
        ],
    ],
)]
#[ORM\Entity(repositoryClass: FollowRepository::class)]
#[AppAssert\UniquePropertyAssociation(
    properties: ['user', 'following'],
    groups: [self::GROUP_WRITE]
)]
class Follow
{
    public const GROUP_READ = 'follow:read';
    public const GROUP_WRITE = 'follow:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Ignore]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups([self::GROUP_READ])]
    #[Assert\NotNull(groups: [self::GROUP_WRITE])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups([self::GROUP_READ])]
    #[Assert\NotNull(groups: [self::GROUP_WRITE])]
    #[AppAssert\NotCurrentUser(groups: [self::GROUP_WRITE])]
    private ?User $following = null;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getFollowing(): ?User
    {
        return $this->following;
    }

    public function setFollowing(User $following): self
    {
        $this->following = $following;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
