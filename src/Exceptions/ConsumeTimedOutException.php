<?php

namespace Scaleplan\Kafka\Exceptions;

/**
 * Class ConsumeTimedOutException
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class ConsumeTimedOutException extends KafkaException
{
    public const MESSAGE = 'kafka.message-received-time-out';
    public const CODE = 408;
}
