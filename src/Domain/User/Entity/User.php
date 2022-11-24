<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Traits\IdTrait;
use App\Application\Common\Traits\SoftDeletableTrait;
use App\Application\Common\Traits\TimestampableTrait;
use App\Application\Common\Traits\UuidTrait;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Validator as UserValidator;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\Cache]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('uuid')]
#[UniqueEntity('email')]
#[UserValidator\AdminDemote]
class User implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    use IdTrait;
    use UuidTrait;
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[Assert\Email,
        Assert\NotBlank,
        Assert\Length(max: 255)]
    #[Groups(['user:read'])]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: false)]
    private string $email;

    #[Assert\Type(Types::STRING),
        Assert\NotBlank,
        Assert\Length(max: 255)]
    #[Groups(['user:read', 'article:read'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $username;

    #[Assert\NotNull]
    #[Groups(['user:read'])]
    #[ORM\ManyToOne(targetEntity: Role::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role;

    #[Ignore]
    #[Assert\Type(Types::STRING)]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private ?string $password = null;

    #[Ignore]
    #[Assert\Length(max: 255)]
    private ?string $plainPassword = null;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getRoles(): array
    {
        /** @phpstan-ignore-next-line  */
        return [$this->role->__toString()];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}
