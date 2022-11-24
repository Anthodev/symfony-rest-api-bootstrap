<?php

declare(strict_types=1);

namespace App\Domain\User\Builder;

use App\Application\Common\Builder\BaseBuilder;
use App\Application\Common\Exception\EntityNotFoundException;
use App\Application\Common\Exception\InvalidArgumentException;
use App\Domain\User\Dto\UserRegistrationInputDto;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Fetcher\RoleFetcherInterface;
use App\Domain\User\Fetcher\UserFetcherInterface;
use App\Domain\User\Service\HashPassword;
use Symfony\Contracts\Service\Attribute\Required;

class UserBuilder extends BaseBuilder implements UserBaseBuilderInterface
{
    #[Required]
    public UserFetcherInterface $userFetcher;

    #[Required]
    public RoleFetcherInterface $roleFetcher;

    #[Required]
    public HashPassword $hashPassword;

    /**
     * @throws InvalidArgumentException|EntityNotFoundException
     */
    public function buildForRegistration(UserRegistrationInputDto $userRegistrationInput): User
    {
        $email = $userRegistrationInput->getEmail();
        $username = $userRegistrationInput->getUsername();
        $password = $userRegistrationInput->getPassword();
        $passwordConfirmation = $userRegistrationInput->getPasswordConfirmation();

        if ($password !== $passwordConfirmation) {
            throw new InvalidArgumentException('Password confirmation does not match');
        }

        if (null === $email || null === $username || null === $password) {
            throw new InvalidArgumentException('The email, username and password cannot be null.');
        }

        $user = $this->userFetcher->findOneBy(['username' => $userRegistrationInput->getUsername()]);

        if ($user instanceof User) {
            throw new InvalidArgumentException('User already exists');
        }

        $userCount = $this->userFetcher->getUserCount();

        if ($userCount === 0) {
            $role = $this->roleFetcher->findOneBy(['code' => RoleCodeEnum::ROLE_ADMIN->value]);
        } else {
            $role = $this->roleFetcher->findOneBy(['code' => RoleCodeEnum::ROLE_USER->value]);
        }

        if (null === $role) {
            throw new EntityNotFoundException('A role should be found.');
        }

        $user = UserFactory::create(
            [
                'email' => $email,
                'username' => $username,
                'role' => $role,
                'password' => $password,
            ]
        );

        $user->SetPassword($this->hashPassword->hash($user));
        $user->eraseCredentials();

        return $user;
    }

    /**
     * @param array<string, mixed> $input
     *
     * @throws EntityNotFoundException
     */
    public function populateWithRole(array $input, User $user): User
    {
        /** @var User $user */
        $user = $this->populate($input, $user);

        $inputRole = $input['role'] ?? null;

        if (null === $inputRole) {
            return $user;
        }

        $role = $this->roleFetcher->find($inputRole);

        if (null === $role) {
            throw new EntityNotFoundException('A role should be found.');
        }

        $user->setRole($role);

        return $user;
    }
}
