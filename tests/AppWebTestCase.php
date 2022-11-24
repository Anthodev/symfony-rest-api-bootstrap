<?php

declare(strict_types=1);

namespace Tests;

use App\Application\Common\Manager\JwtPayloadManager;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use Exception;
use JsonException;
use stdClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Tests\DataFixtures\User\UserFixtures;
use Tests\Traits\AliceLoaderTrait;
use Tests\Traits\TestWebUtilsTrait;

use function json_decode;

class AppWebTestCase extends WebTestCase
{
    use AliceLoaderTrait;
    use TestWebUtilsTrait;

    protected static ?KernelBrowser $client;
    protected static string $uri = '/api';
    protected string $dataFixtureDir;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        static::$client = static::createClient();

        /** @phpstan-ignore-next-line */
        $this->dataFixtureDir = static::$client->getContainer()->getParameter('kernel.project_dir') . '/tests/DataFixtures';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        gc_collect_cycles();
    }

    protected function createAuthenticatedClient(string $email): void
    {
        static::$client = static::createClient();
        $this->loginUser($email);
    }

    public function loginUser(
        string $email = UserFixtures::USER_ADMIN_EMAIL,
    ): void {
        /** @var UserRepository $repository */
        $repository = static::$client->getContainer()->get(UserRepository::class);

        /** @var User|UserInterface $user */
        $user = $repository->findOneBy(['email' => $email]);

        static::$client->loginUser($user);

        /** @var JwtPayloadManager $jwtPayloadManager */
        $jwtPayloadManager = static::$client->getContainer()->get(JwtPayloadManager::class);
        $token = $jwtPayloadManager->getJwtToken();

        static::$client->setServerParameter('HTTP_authorization', 'Bearer '.$token);
    }

    public function getUser(): User
    {
        $security = $this->getSecurity();
        $user = $security->getUser();

        static::assertInstanceOf(User::class, $user);

        return $user;
    }

    /**
     * @throws JsonException
     */
    public function getObjectResponseWithNoError(
        ?string $data = '',
        ?string $method = Request::METHOD_GET,
        ?string $url = null,
    ): stdClass {
        $content = $this->doRequest($data, $method, $url);
        static::assertResponseIsSuccessful();

        /** @var stdClass $response */
        $response = json_decode($content, false, 512, JSON_THROW_ON_ERROR);

        if (is_array($response)) {
            return $response;
        }

        static::assertObjectNotHasAttribute(
            'errors',
            $response,
            isset($response->errors) ? (string)print_r($response->errors[0], false) : '',
        );

        return $response;
    }

    /**
     * @throws JsonException
     */
    public function getObjectResponseWithError(
        ?string $data = '',
        ?string $method = Request::METHOD_GET,
        ?string $url = null,
    ): \stdClass {
        $content = $this->doRequest($data, $method, $url);

        /** @var \stdClass $response */
        $response = json_decode($content, false, 512, JSON_THROW_ON_ERROR);

        if (isset($response->title)) {
            static::assertSame('An error occurred', $response->title);
        } elseif (isset($response->code)) {
            static::assertContains(substr((string) $response->code, 0, 1), ['4', '5']);
        }

        return $response;
    }

    /**
     * @throws \JsonException
     * @return array<string, mixed>
     */
    public function getArrayResponseWithNoError(
        ?string $data = '',
        ?string $method = Request::METHOD_GET,
        ?string $url = null,
    ): array {
        $content = $this->doRequest($data, $method, $url);

        /** @var array<string, mixed> $response */
        $response = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (isset($response['title'])) {
            static::assertNotSame('An error occurred', $response['title']);
        } elseif (isset($response['status'])) {
            static::assertNotContains(substr((string) $response['status'], 0, 1), ['4', '5']);
        } else {
            static::assertArrayNotHasKey('errors', $response);
        }

        return $response;
    }

    public function doRequest(
        ?string $data = '',
        ?string $method = Request::METHOD_GET,
        ?string $url = null,
    ): string {
        static::$client->request(
            method: $method,
            uri: static::$uri . $url,
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            content: $data,
        );

        $content = (string)static::$client->getResponse()->getContent();
        static::assertJson($content);

        return $content;
    }
}
