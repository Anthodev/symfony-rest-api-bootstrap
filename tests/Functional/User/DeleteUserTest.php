<?php

declare(strict_types=1);

namespace Tests\Functional\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\User\UserFixtures;

beforeEach(function () {
    $this->loadBaseFixturesOnly();
});

it('can delete myself', function (): void {
    $this->loginUser();

    $this->getObjectResponseWithNoError(
        method: Request::METHOD_DELETE,
        url: sprintf('/user/%s', UserFixtures::USER_ADMIN_UUID),
    );
});

it('can delete another user', function (): void {
    $this->loginUser();

    $this->getObjectResponseWithNoError(
        method: Request::METHOD_DELETE,
        url: sprintf('/user/%s', UserFixtures::USER1_USER_UUID),
    );
});

it('can delete myself from normal user', function (): void {
    $this->loginUser(UserFixtures::USER1_USER_EMAIL);

    $this->getObjectResponseWithNoError(
        method: Request::METHOD_DELETE,
        url: sprintf('/user/%s', UserFixtures::USER1_USER_UUID),
    );
});

it('cannot delete another user as normal user', function (): void {
    $this->loginUser(UserFixtures::USER1_USER_EMAIL);

    $response = $this->getObjectResponseWithError(
        method: Request::METHOD_DELETE,
        url: sprintf('/user/%s', UserFixtures::USER_ADMIN_UUID),
    );

    expect($response)
        ->status->toBe(Response::HTTP_FORBIDDEN)
        ->detail->toBe('Access Denied.')
    ;
});
