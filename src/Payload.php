<?php

namespace Scaleplan\Kafka;

/**
 * Class Payload
 *
 * @package Scaleplan\Event\Kafka
 */
class Payload
{
    /**
     * @var string
     */
    protected $topicName;

    /**
     * @var string
     */
    protected $data;

    /**
     * Payload constructor.
     *
     * @param string $topicName
     * @param string $data
     */
    public function __construct(string $topicName, string $data)
    {
        $this->topicName = $topicName;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getTopicName() : string
    {
        return $this->topicName;
    }

    /**
     * @return string
     */
    public function getData() : string
    {
        return $this->data;
    }
}
