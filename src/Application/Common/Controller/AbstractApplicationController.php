<?php

declare(strict_types=1);

namespace App\Application\Common\Controller;

use const JSON_THROW_ON_ERROR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractApplicationController extends AbstractController
{
    #[Required]
    public SerializerInterface $serializer;

    /**
     * @return array<string, mixed>
     * @throws \JsonException
     */
    public function deserialize(string $data): array
    {
        /** @var array<string, mixed> */
        return \json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param list<object>|object $data
     * @param array<string, mixed> $context
     * @param list<string> $groups
     */
    public function output(
        object|array $data,
        array $context = [],
        array $groups = [],
    ): Response {
        $groups = array_merge($groups, ['all:read']);

        $context[] = (new ObjectNormalizerContextBuilder())
            ->withGroups($groups)
            ->toArray()
        ;

        return $this->json($data, context: $context);
    }

    /**
     * @param list<string> $groups
     */
    public function setObjectContext(array $groups): ObjectNormalizerContextBuilder
    {
        return (new ObjectNormalizerContextBuilder())
            ->withGroups($groups)
        ;
    }
}
