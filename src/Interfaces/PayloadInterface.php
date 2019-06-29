<?php

namespace Scaleplan\Kafka\Interfaces;

/**
 * Class Payload
 *
 * @package Scaleplan\Event\Kafka
 */
interface PayloadInterface
{
    /**
     * @return string
     */
    public function getTopicName() : string;

    /**
     * @return string
     */
    public function getData() : string;
}
