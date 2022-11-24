<?php

declare(strict_types=1);

namespace App\Domain\User\Validator;

use App\Application\Common\Exception\UnexpectedTypeException;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Fetcher\RoleFetcherInterface;
use App\Domain\User\Fetcher\UserFetcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Service\Attribute\Required;

class AdminDemoteValidator extends ConstraintValidator
{
    #[Required]
    public UserFetcherInterface $userFetcher;

    #[Required]
    public RoleFetcherInterface $roleFetcher;

    #[Required]
    public EntityManagerInterface $entityManager;

    /**
     * @inheritDoc
     * @throws UnexpectedTypeException
     */
    public function validate(mixed $user, Constraint $constraint): void
    {
        if (!$user instanceof User) {
            throw new UnexpectedTypeException($user, User::class);
        }

        if (!$constraint instanceof AdminDemote) {
            throw new UnexpectedTypeException($constraint, AdminDemote::class);
        }

        if (null === $user->getUuid()) {
            return;
        }

        /** @var User $previousUser */
        $previousUser = $this->entityManager->getUnitOfWork()->getOriginalEntityData($user);

        $role = $previousUser['role'] ?? null;

        if (null === $role) {
            return;
        }

        if ($role->getCode() !== RoleCodeEnum::ROLE_ADMIN->value) {
            return;
        }

        $currentRole = $user->getRole();

        if (null === $currentRole) {
            return;
        }

        $roleAdmin = $this->roleFetcher->findOneBy(['code' => RoleCodeEnum::ROLE_ADMIN->value]);
        $userAdminCount = $this->userFetcher->findBy(['role' => $roleAdmin]);

        if (
            $currentRole->getCode() !== RoleCodeEnum::ROLE_ADMIN->value
            && count($userAdminCount) <= 1
        ) {
            $this->context->buildViolation($constraint->message)
                ->atPath('user.role')
                ->setCode(AdminDemote::USER_ADMIN_DEMOTE_ERROR_CODE)
                ->addViolation();
        }
    }
}
