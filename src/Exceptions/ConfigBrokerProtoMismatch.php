<?php

namespace Scaleplan\Kafka\Exceptions;

/**
 * Class ConfigBrokerProtoMismatch
 *
 * @package Scaleplan\Kafka\Exceptions
 */
class ConfigBrokerProtoMismatch extends AbstractException
{
    public const MESSAGE = 'Kafka config broker proto mismatch.';
}