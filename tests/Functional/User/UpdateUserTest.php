<?php

declare(strict_types=1);

namespace Tests\Functional\User;

use App\Domain\User\Enum\RoleCodeEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ReferenceData\User\RoleFixtures;
use Tests\DataFixtures\User\UserFixtures;

beforeEach(function () {
    $this->loadBaseFixturesOnly();
    $this->loginUser();
});

it('can update myself', function (): void {
    $response = $this->getObjectResponseWithNoError(
        data: sprintf(
            '
                {
                    "username": "%s",
                    "email": "%s"
                }
            ',
            'Updated',
            'updated@test.io',
        ),
        method: Request::METHOD_PATCH,
        url: sprintf('/user/%s', UserFixtures::USER_ADMIN_UUID),
    );

    expect($response)
        ->username->toBe('Updated')
        ->email->toBe('updated@test.io')
        ->role->code->toBe(RoleCodeEnum::ROLE_ADMIN->value)
    ;
});

it('can update another user', function (): void {
    $response = $this->getObjectResponseWithNoError(
        data: sprintf(
            '
                {
                    "username": "%s",
                    "email": "%s"
                }
            ',
            'Updated',
            'updated@test.io',
        ),
        method: Request::METHOD_PATCH,
        url: sprintf('/user/%s', UserFixtures::USER1_USER_UUID),
    );

    expect($response)
        ->username->toBe('Updated')
        ->email->toBe('updated@test.io')
    ;
});

it('can update myself from normal user', function (): void {
    $this->loginUser(UserFixtures::USER1_USER_EMAIL);

    $response = $this->getObjectResponseWithNoError(
        data: sprintf(
            '
                {
                    "username": "%s",
                    "email": "%s"
                }
            ',
            'Updated',
            'updated@test.io',
        ),
        method: Request::METHOD_PATCH,
        url: sprintf('/user/%s', UserFixtures::USER1_USER_UUID),
    );

    expect($response)
        ->username->toBe('Updated')
        ->email->toBe('updated@test.io')
    ;
});

it('cannot update another user when normal user', function (): void {
    $this->loginUser(UserFixtures::USER1_USER_EMAIL);

    $response = $this->getObjectResponseWithError(
        data: sprintf(
            '
                {
                    "username": "%s",
                    "email": "%s"
                }
            ',
            'Updated',
            'updated@test.io',
        ),
        method: Request::METHOD_PATCH,
        url: sprintf('/user/%s', UserFixtures::USER_ADMIN_UUID),
    );

    expect($response)
        ->status->toBe(Response::HTTP_FORBIDDEN)
        ->detail->toBe('Access Denied.')
    ;
});

it('cannot demote an admin when no admin left', function (): void {
    $response = $this->getObjectResponseWithError(
        data: sprintf(
            '
                {
                    "role": "%s"
                }
            ',
            RoleFixtures::ROLE_USER_UUID
        ),
        method: Request::METHOD_PATCH,
        url: sprintf('/user/%s', UserFixtures::USER_ADMIN_UUID),
    );

    expect($response)
        ->status->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->detail
            ->toContain('You cannot demote yourself without another admin.')
            ->toContain('01592fb9-a6ed-4577-950e-09d093f1468f')
    ;
});
