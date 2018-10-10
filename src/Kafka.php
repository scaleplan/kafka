<?php

namespace Scaleplan\Event\KafkaSupport;

use RdKafka\{
    Consumer, ConsumerTopic, ProducerTopic, Topic, Producer
};
use Scaleplan\Kafka\Config;
use Scaleplan\Kafka\Exceptions\ConfigParseException;
use Scaleplan\Kafka\Exceptions\ConsumerNotFoundException;
use Scaleplan\Kafka\Node;
use Scaleplan\Kafka\Payload;
use Symfony\Component\Yaml\{
    Yaml, Exception\ParseException
};

/**
 * Class Kafka
 *
 * @package Scaleplan\Event
 */
class Kafka
{
    public const LOG_LEVEL = LOG_WARNING;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \RdKafka\ProducerTopic
     */
    protected $producerTopic;

    /**
     * @var \RdKafka\ConsumerTopic
     */
    protected $consumerTopic;

    /**
     * @var bool
     */
    protected $isConsumeStart = false;

    /**
     * @var Node
     */
    protected $currentNode;

    /**
     * @var int
     */
    protected $messageCount = 0;

    /**
     * Kafka constructor.
     *
     * @param string $name
     * @param string $confPath
     *
     * @throws \ReflectionException
     * @throws ConfigParseException
     */
    public function __construct(string $name, string $confPath = '../Config/kafka.yml')
    {
        $this->name = $name;
        try {
            $settings = Yaml::parseFile($confPath);
        } catch (ParseException $e) {
            throw new ConfigParseException($e->getMessage());
        }

        $this->config = new Config($settings);
    }

    /**
     * @return \RdKafka\ProducerTopic
     */
    protected function getProducerTopic() : ProducerTopic
    {
        if ($this->producerTopic) {
            $brokerString = implode(',', $this->config->getBrokers());
            $producer = new Producer();
            $producer->setLogLevel(static::LOG_LEVEL);
            $producer->addBrokers($brokerString);

            $this->producerTopic = $producer->newTopic($this->name);
        }

        return $this->producerTopic;
    }

    /**
     * @return \RdKafka\ConsumerTopic
     */
    protected function getConsumerTopic() : ConsumerTopic
    {
        if ($this->consumerTopic) {
            $brokerString = implode(',', $this->config->getBrokers());
            $consumer = new Consumer();
            $consumer->setLogLevel(LOG_WARNING);
            $consumer->addBrokers($brokerString);

            $this->consumerTopic = $consumer->newTopic($this->name);
        }

        return $this->consumerTopic;
    }

    /**
     * @param Node $consumer
     * @param Payload $payload
     *
     * @throws ConsumerNotFoundException
     */
    public function produceTo(Node $consumer, Payload $payload) : void
    {
        if (!\in_array($consumer, $this->config->getConsumers(), true)) {
            throw new ConsumerNotFoundException();
        }

        $this->getProducerTopic()->produce($consumer->getId(), 0, (string) $payload);
    }

    /**
     * @return Payload
     */
    public function getMessage() : Payload
    {
        if (!$this->isConsumeStart) {
            $this->getConsumerTopic()->consumeStart($this->currentNode->getId(), RD_KAFKA_OFFSET_STORED);
        }
        
        $message = $this->getConsumerTopic()->consume($this->currentNode->getId(), $this->config->getTimeout());
        $this->messageCount++;
        return new Payload($message);
    }

    /**
     * Kafka destructor.
     */
    public function __destruct()
    {
        $this->getConsumerTopic()->offsetStore($this->currentNode->getId(), RD_KAFKA_OFFSET_STORED + $this->messageCount);
    }
}