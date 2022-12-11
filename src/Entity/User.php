<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('username')]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const GROUP_DEFAULT = 'Default';
    public const GROUP_READ = 'user:read';
    public const GROUP_CREATE = 'user:create';
    public const GROUP_UPDATE = 'user:update';
    public const GROUP_UPDATE_PASSWORD = 'user:update:password';
    public const GROUP_UPDATE_AVATAR = 'user:update:avatar';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 30, unique: true)]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 30)]
    private ?string $username = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var null|string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(groups: [self::GROUP_CREATE, self::GROUP_UPDATE_PASSWORD])]
    #[Assert\Length(min: 6, groups: [self::GROUP_CREATE, self::GROUP_UPDATE_PASSWORD])]
    #[Groups([self::GROUP_CREATE, self::GROUP_UPDATE_PASSWORD])]
    private ?string $plainPassword = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_UPDATE])]
    private ?string $bio;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_UPDATE_AVATAR])]
    #[Assert\NotBlank(groups: [self::GROUP_UPDATE_AVATAR])]
    private ?string $avatarFilename = null;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private int $postCount = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
        return (string) $this->email;
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

    public function addRole(string $role): self
    {
        $roles = $this->getRoles();
        $roles[] = $role;

        $this->roles = array_unique($roles);

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

    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_USER,
            self::ROLE_ADMIN,
        ];
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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $painPassword): self
    {
        $this->plainPassword = $painPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
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

    public function getPostCount(): int
    {
        return $this->postCount;
    }

    public function setPostCount(int $postCount): self
    {
        $this->postCount = $postCount;
        return $this;
    }
}
