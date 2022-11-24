<?php

declare(strict_types=1);

namespace App\Application\Common\Builder;

use App\Application\Common\Exception\BuilderError;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Base builder for building generic entities object, should be used by any concrete Entity builder in application.
 */
class BaseBuilder implements BaseBuilderInterface
{
    #[Required]
    public LoggerInterface $logger;

    public function __construct(
        protected readonly DenormalizerInterface $serializer,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function build(string $className, array $objectData, array $context = []): object
    {
        try {
            /** @var object */
            return $this->serializer->denormalize($objectData, $className, 'json', $context);
        } catch (ExceptionInterface $exception) {
            $this->logger->debug('Error build denormalize :' . $exception->getMessage(), compact('exception', 'className', 'objectData'));

            throw new BuilderError($exception->getMessage(), previous: $exception);
        }
    }

    public function populate(array $objectData, object $objectToPopulate, array $context = []): object
    {
        try {
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $objectToPopulate;

            /** @var object */
            return $this->serializer->denormalize($objectData, $objectToPopulate::class, 'json', $context);
        } catch (ExceptionInterface $exception) {
            $this->logger->debug('Error populate denormalize :' . $exception->getMessage(), compact('exception', 'objectData', 'objectToPopulate'));

            throw new BuilderError($exception->getMessage(), previous: $exception);
        }
    }
}
