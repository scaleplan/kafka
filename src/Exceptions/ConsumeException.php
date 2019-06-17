<?php

namespace Scaleplan\Kafka\Exceptions;

/**
 * Class ConsumeException
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class ConsumeException extends KafkaException
{
    public const MESSAGE = 'Consume message error.';
    public const CODE = 406;
}
