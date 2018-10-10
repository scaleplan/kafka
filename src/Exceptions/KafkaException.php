<?php

namespace Scaleplan\Kafka\Exceptions;

/**
 * Class KafkaException
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class KafkaException extends \Exception
{
    public const MESSAGE = 'Kafka error.';

    /**
     * KafkaException constructor.
     *
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(static::MESSAGE, $code, $previous);
    }
}