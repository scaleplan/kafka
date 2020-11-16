<?php

namespace Scaleplan\Kafka\Exceptions;

use function Scaleplan\Translator\translate;

/**
 * Class KafkaException
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class KafkaException extends \Exception
{
    public const MESSAGE = 'kafka.kafka-error';
    public const CODE = 500;

    /**
     * KafkaException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     *
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(
            $message ?: translate(static::MESSAGE) ?: static::MESSAGE,
            $code ?: static::CODE,
            $previous
        );
    }
}
