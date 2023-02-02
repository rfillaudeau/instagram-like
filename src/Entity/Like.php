<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata as ApiMethod;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Controller\Api\Like\CreateLike;
use App\Controller\Api\Like\DeleteLike;
use App\Repository\LikeRepository;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new ApiMethod\GetCollection(),
        new ApiMethod\Post(
            uriTemplate: '/posts/{id}/like',
            controller: CreateLike::class,
            security: 'is_granted("' . User::ROLE_USER . '")',
            read: false,
        ),
        new ApiMethod\Delete(
            uriTemplate: '/posts/{id}/like',
            controller: DeleteLike::class,
            security: 'is_granted("' . User::ROLE_USER . '")',
            read: false,
        ),
    ],
    normalizationContext: [
        AbstractNormalizer::GROUPS => [
            self::GROUP_READ,
            User::GROUP_READ,
            Post::GROUP_READ,
        ],
        AbstractObjectNormalizer::SKIP_NULL_VALUES => false,
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
#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: 'post_like')]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'post' => SearchFilterInterface::STRATEGY_EXACT,
        'user' => SearchFilterInterface::STRATEGY_EXACT,
    ]
)]
#[AppAssert\UniquePropertyAssociation(
    properties: ['post', 'user'],
    groups: [self::GROUP_WRITE]
)]
class Like
{
    public const GROUP_READ = 'like:read';
    public const GROUP_WRITE = 'like:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Ignore]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups([self::GROUP_READ])]
    #[Assert\NotNull(groups: [self::GROUP_WRITE])]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups([self::GROUP_READ])]
    #[Assert\NotNull(groups: [self::GROUP_WRITE])]
    private User $user;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
