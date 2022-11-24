<?php

declare(strict_types=1);

namespace App\Application\Common\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait EnabledTrait
{
    #[Groups(['all:read'])]
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $enabled = false;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }
}
