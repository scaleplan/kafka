<?php

namespace Scaleplan\Kafka\Exceptions;

/**
 * Class ConsumeTimedOutException
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class ConsumeTimedOutException extends KafkaException
{
    public const MESSAGE = 'Истекло время получения сообщения.';
    public const CODE = 408;
}
