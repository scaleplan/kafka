<?php

namespace Scaleplan\Kafka\Exceptions;

/**
 * Class ConsumeTimedOutException
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class ConsumeTimedOutException extends KafkaException
{
    public const MESSAGE = 'Consume timed out error.';
}