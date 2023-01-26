<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Filter\OnlyPostsFromFollowingFilter;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            paginationClientItemsPerPage: true,
        ),
        new Get(),
    ],
    normalizationContext: [
        AbstractNormalizer::GROUPS => [
            Post::GROUP_READ,
            User::GROUP_READ,
        ]
    ],
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'createdAt' => Criteria::DESC,
    ],
    arguments: ['orderParameterName' => 'order']
)]
#[ApiFilter(OnlyPostsFromFollowingFilter::class)]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    public const GROUP_DEFAULT = 'Default';
    public const GROUP_READ = 'post:read';
    public const GROUP_CREATE = 'post:create';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::GROUP_READ])]
    private ?string $pictureFilename = null;

    #[Assert\NotNull(groups: [self::GROUP_CREATE])]
    #[Assert\Image(maxSize: '3m', maxWidth: 3000, maxHeight: 3000)]
    private ?File $picture = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups([self::GROUP_READ])]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([self::GROUP_READ])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([self::GROUP_READ])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups([self::GROUP_READ])]
    #[MaxDepth(1)]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private int $likeCount = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPictureFilename(): ?string
    {
        return $this->pictureFilename;
    }

    public function setPictureFilename(string $pictureFilename): self
    {
        $this->pictureFilename = $pictureFilename;

        return $this;
    }

    public function getPicture(): ?File
    {
        return $this->picture;
    }

    public function setPicture(?File $picture): Post
    {
        $this->picture = $picture;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLikeCount(): ?int
    {
        return $this->likeCount;
    }

    public function setLikeCount(int $likeCount): self
    {
        $this->likeCount = $likeCount;

        return $this;
    }
}
