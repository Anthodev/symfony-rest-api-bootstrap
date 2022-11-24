<?php

declare(strict_types=1);

namespace App\Domain\User\Builder;

use App\Application\Common\Builder\BaseBuilderInterface;
use App\Domain\User\Dto\UserRegistrationInputDto;
use App\Domain\User\Entity\User;

interface UserBaseBuilderInterface extends BaseBuilderInterface
{
    public function buildForRegistration(UserRegistrationInputDto $userRegistrationInput): User;

    /** @param array<string, mixed> $input */
    public function populateWithRole(array $input, User $user): User;
}
