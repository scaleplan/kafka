<?php

namespace Scaleplan\Kafka\Interfaces;

use RdKafka\Conf;
use RdKafka\Producer;
use Scaleplan\Kafka\Payload;

/**
 * Class Kafka
 *
 * @package Scaleplan\Event
 */
interface KafkaInterface
{
    /**
     * @param string $topicName
     * @param string $data
     */
    public function produce(string $topicName, string $data) : void;

    /**
     * @return Conf
     */
    public function getConf() : Conf;

    /**
     * @return null|Payload
     */
    public function getMessage() : ?Payload;

    /**
     * @param array $consumerTopics
     */
    public function setConsumerTopics(array $consumerTopics) : void;

    /**
     * @return Producer
     */
    public function getProducer() : Producer;
}
