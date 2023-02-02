<?php

namespace App\Entity;

use ApiPlatform\Metadata as ApiMethod;
use ApiPlatform\Metadata\ApiResource;
use App\Controller\Api\AccessToken\RevokeAccessToken;
use App\Dto\LoginDto;
use App\Repository\AccessTokenRepository;
use App\State\LoginProcessor;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[ApiResource(
    operations: [
        new ApiMethod\Post(
            uriTemplate: '/auth/token',
            input: LoginDto::class,
            processor: LoginProcessor::class,
        ),
        new ApiMethod\Delete(
            uriTemplate: '/auth/revoke',
            controller: RevokeAccessToken::class,
            security: 'is_granted("' . User::ROLE_USER . '")',
            read: false,
        ),
    ],
    normalizationContext: [
        AbstractNormalizer::GROUPS => [
            self::GROUP_READ,
        ],
    ],
)]
#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
class AccessToken
{
    public const GROUP_READ = 'access_token:read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Ignore]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(self::GROUP_READ)]
    private string $token;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(self::GROUP_READ)]
    private DateTimeInterface $expiresAt;

    public function __construct()
    {
        $this->token = bin2hex(random_bytes(60));
        $this->expiresAt = new DateTime('+1 day');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
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

    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function isValid(): bool
    {
        return $this->expiresAt > new DateTime();
    }
}
