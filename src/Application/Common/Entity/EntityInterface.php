<?php

declare(strict_types=1);

namespace App\Application\Common\Entity;

use Symfony\Component\Uid\Uuid;

interface EntityInterface
{
    public function getId(): ?int;

    public function getUuid(): ?Uuid;

    public function setUuid(string|Uuid $uuid): static;

    public function setDefaultUuid(): static;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setCreatedAt(): static;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(): static;
}
