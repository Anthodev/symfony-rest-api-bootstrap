<?php

declare(strict_types=1);

namespace App\Domain\User\Security;

use App\Application\Common\Helper\EntityInterfaceHelper;
use App\Application\Common\Security\AbstractVoter;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class UserVoter extends AbstractVoter
{
    #[Required]
    public EntityManagerInterface $entityManager;

    public const ADD = 'add-user';
    public const EDIT = 'edit-user';
    public const DELETE = 'delete-user';

    protected string $entityClass = User::class;

    public function __construct()
    {
        $this->attributes = [
            self::ADD,
            self::EDIT,
            self::DELETE,
        ];
    }

    public function canAddUser(?User $subject, User $user): bool
    {
        return $this->contextProvider->getContextUserRoleCode() === RoleCodeEnum::ROLE_ADMIN->value;
    }

    public function canEditUser(User $subject, User $user): bool
    {
        return
            $this->contextProvider->getContextUserRoleCode() === RoleCodeEnum::ROLE_ADMIN->value
            || EntityInterfaceHelper::areTheSame($subject, $user)
        ;
    }

    public function canDeleteUser(User $subject, User $user): bool
    {
        return $this->canEditUser($subject, $user);
    }
}
