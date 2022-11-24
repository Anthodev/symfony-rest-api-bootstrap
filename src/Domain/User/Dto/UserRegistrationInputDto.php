<?php

declare(strict_types=1);

namespace App\Domain\User\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationInputDto
{
    public function __construct(
        #[Assert\Email]
        #[Assert\Length(max: 255)]
        private ?string $email = null,
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        private ?string $username = null,
        #[Assert\NotBlank]
        private ?string $password = null,
        private ?string $passwordConfirmation = null,
    ) {
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordConfirmation(): ?string
    {
        return $this->passwordConfirmation;
    }

    public function setPasswordConfirmation(?string $passwordConfirmation): static
    {
        $this->passwordConfirmation = $passwordConfirmation;

        return $this;
    }
}
