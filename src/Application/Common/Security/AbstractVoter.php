<?php

declare(strict_types=1);

namespace App\Application\Common\Security;

use App\Application\Common\Entity\EntityInterface;
use App\Application\Common\Provider\ContextProvider;
use App\Domain\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractVoter extends Voter implements VoterInterface
{
    #[Required]
    public Security $security;

    #[Required]
    public ContextProvider $contextProvider;

    /** @var list<string> */
    protected array $attributes = [];
    protected string $entityClass;

    /** @return list<string> */
    public function getAttributes(): array
    {
        /** @var list<string> */
        return array_keys($this->attributes);
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * {@inheritDoc}
     * @param EntityInterface|null $subject
     */
    public function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, $this->attributes, true)) {
            return false;
        }

        /** @phpstan-ignore-next-line */
        if ($subject !== null && !is_int($subject) && !$subject instanceof $this->entityClass) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        $func = sprintf('can%s', $this->convertSnakeCaseToCamelCase($attribute));

        if (!method_exists($this, $func)) {
            throw new \LogicException(sprintf('No method "%s:%s" found to handle this attribute !', $this::class, $func));
        }

        return $this->$func($subject, $user);
    }

    protected function convertSnakeCaseToCamelCase(string $key): string
    {
        return str_replace('-', '', ucwords($key, '-'));
    }
}
