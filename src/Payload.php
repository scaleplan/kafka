<?php

namespace Scaleplan\Kafka;

use RdKafka\Message;

/**
 * Class Payload
 *
 * @package Scaleplan\Event\Kafka
 */
class Payload
{
    /**
     * @var Node
     */
    protected $from;

    /**
     * Payload constructor.
     *
     * @param \RdKafka\Message $message
     */
    public function __construct(Message $message)
    {
    }
}