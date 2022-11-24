<?php

declare(strict_types=1);

namespace Tests\Integration\User;

use App\Application\Common\Exception\EntityValidationException;
use App\Application\Common\Service\EntityValidationService;
use App\Domain\User\Entity\Role;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\RoleCodeEnum;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\DataFixtures\User\UserFixtures;
use Tests\Traits\AliceLoaderTrait;

class AdminDemoteValidatorTest extends KernelTestCase
{
    use AliceLoaderTrait;

    private readonly EntityValidationService $validator;
    private readonly EntityManagerInterface $entityManager;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->validator = $container->get(EntityValidationService::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);


        parent::setUp();
    }

    /**
     * @throws \JsonException
     */
    public function testCannotDemoteAdminWhenNoAdminLeft(): void
    {
        $this->loadBaseFixturesOnly();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserFixtures::USER_ADMIN_EMAIL]);

        $roleUser = $this->entityManager->getRepository(Role::class)->findOneBy(['code' => RoleCodeEnum::ROLE_USER]);

        $user->setRole($roleUser);

        try {
            $this->validator->validateEntity(entity: $user, groups: [(new ReflectionClass(User::class))->getShortName()]);
            self::fail('EntityValidationException was expected');
        } catch (EntityValidationException $e) {
            self::assertArrayHasKey('user.role', $e->getErrors()->toArray());
            self::assertCount(1, $e->getErrors()->toArray()['user.role']);
            self::assertSame('You cannot demote yourself without another admin.', $e->getErrors()->toArray()['user.role'][0]);
        }
    }
}
