<?php

namespace Scaleplan\Kafka\Exceptions;

/**
 * Class ConfigParseException
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class ConfigParseException extends KafkaException
{
    public const MESSAGE = 'Kafka config parse error.';
}