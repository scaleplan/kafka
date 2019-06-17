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
    public const CODE = 500;

    /**
     * KafkaException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message ?: static::MESSAGE, $code ?: static::CODE, $previous);
    }
}
