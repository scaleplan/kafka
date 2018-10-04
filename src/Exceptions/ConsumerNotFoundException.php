<?php

namespace Scaleplan\Kafka\Exceptions;

/**
 * Class ConsumerNotFoundException
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class ConsumerNotFoundException extends AbstractException
{
    public const MESSAGE = 'Consumer not found.';
}