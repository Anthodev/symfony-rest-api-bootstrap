<?php

declare(strict_types=1);

namespace App\Application\Common\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait LabelTrait
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['all:read'])]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: false)]
    private string $label;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
