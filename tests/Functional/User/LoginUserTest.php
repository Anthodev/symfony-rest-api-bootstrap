<?php

declare(strict_types=1);

namespace Tests\Functional\User;

use Symfony\Component\HttpFoundation\Request;

it('can login a user', function (): void {
    $this->loadBaseFixturesOnly();

    $response = $this->getObjectResponseWithNoError(
        data: '
            {
                "email": "admin@test.io",
                "password": "Test@1234"
            }
        ',
        method: Request::METHOD_POST,
        url: '/login_check',
    );

    expect($response->token)->not()->toBeNull();
});

it('cannot login a user with wrong password', function (): void {
    $this->loadBaseFixturesOnly();

    $response = $this->getObjectResponseWithError(
        data: '
            {
                "email": "admin@test.io",
                "password": "test"
            }
        ',
        method: Request::METHOD_POST,
        url: '/login_check',
    );

    expect($response->code)->toBe(401);
});
