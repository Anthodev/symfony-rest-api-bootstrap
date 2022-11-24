<?php

declare(strict_types=1);

namespace App\Application\Common\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

trait UuidTrait
{
    #[Assert\NotBlank(
        groups: ['all:read'],
    )]
    #[Groups(['all:read'])]
    #[ORM\Column(type: 'uuid', unique: true, nullable: false)]
    private ?Uuid $uuid = null;

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(string|Uuid $uuid): static
    {
        $this->uuid = Uuid::fromString((string) $uuid);

        return $this;
    }

    #[Ignore]
    #[ORM\PrePersist]
    public function setDefaultUuid(): static
    {
        if (null === $this->getUuid()) {
            $this->uuid = Uuid::v4();
        }

        return $this;
    }
}
