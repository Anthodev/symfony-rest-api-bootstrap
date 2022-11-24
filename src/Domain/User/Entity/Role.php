<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Traits\CodeTrait;
use App\Application\Common\Traits\IdTrait;
use App\Application\Common\Traits\LabelTrait;
use App\Application\Common\Traits\TimestampableTrait;
use App\Application\Common\Traits\UuidTrait;
use App\Domain\User\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[UniqueEntity('uuid')]
#[UniqueEntity('code')]
#[UniqueEntity('label')]
#[ORM\HasLifecycleCallbacks]
class Role implements EntityInterface
{
    use IdTrait;
    use UuidTrait;
    use CodeTrait;
    use LabelTrait;
    use TimestampableTrait;

    public function __toString(): string
    {
        return $this->code;
    }
}
