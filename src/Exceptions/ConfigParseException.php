<?php

namespace Scaleplan\Kafka\Exceptions;

/**
 * Class ConfigParseException
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class ConfigParseException extends AbstractException
{
    public const MESSAGE = 'Kafka config parse error.';
}