<?php

namespace Scaleplan\Kafka\Exceptions;

/**
 * Class ConsumeException
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class ConsumeException extends KafkaException
{
    public const MESSAGE = 'Ошибка получения сообщения.';
    public const CODE = 406;
}
