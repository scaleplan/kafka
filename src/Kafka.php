<?php

namespace Scaleplan\Kafka;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Producer;
use RdKafka\ProducerTopic;
use RdKafka\TopicConf;
use Scaleplan\Kafka\Exceptions\ConsumeException;
use Scaleplan\Kafka\Exceptions\ConsumeTimedOutException;
use function Scaleplan\Helpers\getenv;

/**
 * Class Kafka
 *
 * @package Scaleplan\Event
 */
class Kafka
{
    public const LOG_LEVEL = LOG_WARNING;
    public const TIMEOUT = 1e4;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var string
     */
    protected $brokers;

    /**
     * @var array
     */
    protected $consumerTopics;

    /**
     * @var Producer
     */
    protected $producer;

    /**
     * @var KafkaConsumer
     */
    protected $consumer;

    /**
     * @var \RdKafka\ProducerTopic
     */
    protected $producerTopic;

    /**
     * @var \RdKafka\ConsumerTopic
     */
    protected $consumerTopic;

    /**
     * @var array|Kafka[]
     */
    protected static $instances;

    /**
     * @param array|null $consumerTopics
     *
     * @return Kafka
     */
    public static function getInstance(array $consumerTopics = null) : Kafka
    {
        $key = serialize($consumerTopics);
        if (!array_key_exists($key, static::$instances)) {
            static::$instances[$key] = static($consumerTopics);
        }

        return static::$instances[$key];
    }

    /**
     * Kafka constructor.
     *
     * @param array|null $consumerTopics
     */
    protected function __construct(array $consumerTopics = null)
    {
        $this->consumerTopics = $consumerTopics ?? array_map(function(string $item) {
                return trim($item);
            }, explode(',', getenv('KAFKA_CONSUMER_TOPICS')));
        $this->brokers = getenv('KAFKA_BROKERS');
        $this->timeout = getenv('KAFKA_TIMEOUT') ?? static::TIMEOUT;
    }

    /**
     * @param string $topicName
     *
     * @return ProducerTopic
     */
    protected function getProducerTopic(string $topicName) : ProducerTopic
    {
        if ($this->producerTopic) {
            $this->producer = new Producer();
            $this->producer->setLogLevel(static::LOG_LEVEL);
            $this->producer->addBrokers($this->brokers);

            $this->producerTopic = $this->producer->newTopic($topicName);
        }

        return $this->producerTopic;
    }

    /**
     * @param string $topicName
     * @param array $data
     */
    public function produce(string $topicName, array $data) : void
    {
        $this->getProducerTopic($topicName)->produce(
            RD_KAFKA_PARTITION_UA,
            0,
            json_encode($data, JSON_OBJECT_AS_ARRAY | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION)
        );
        $this->producer->poll(0);
    }

    /**
     * @return Conf
     */
    public function getConf() : Conf
    {
        static $conf;
        if (!$conf) {
            $conf = new Conf();
        }

        return $conf;
    }

    /**
     * @return KafkaConsumer
     *
     * @throws \RdKafka\Exception
     */
    protected function getConsumer() : KafkaConsumer
    {
        if (!$this->consumer) {
            $conf = $this->getConf();
            $conf->set('group.id', 'myConsumerGroup');
            $conf->set('metadata.broker.list', $this->brokers);

            $topicConf = new TopicConf();
            $topicConf->set('auto.offset.reset', 'smallest');
            $conf->setDefaultTopicConf($topicConf);
            $this->consumer = new KafkaConsumer($conf);
            if ($this->consumerTopics) {
                $this->consumer->subscribe($this->consumerTopics);
            }
        }

        return $this->consumer;
    }

    /**
     * @return null|Payload
     * 
     * @throws ConsumeException
     * @throws ConsumeTimedOutException
     * @throws \RdKafka\Exception
     */
    public function getMessage() : ?Payload
    {
        $message = $this->getConsumer()->consume($this->timeout);
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                return new Payload($message->topic_name, $message->payload);
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                return null;
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                throw new ConsumeTimedOutException();
                break;
            default:
                throw new ConsumeException($message->errstr(), $message->err);
                break;
        }
    }

    /**
     * @throws \RdKafka\Exception
     */
    public function __destruct()
    {
        $this->getConsumer()->commitAsync();
    }

    /**
     * @param array $consumerTopics
     */
    public function setConsumerTopics(array $consumerTopics) : void
    {
        $this->consumerTopics = $consumerTopics;
    }

    /**
     * @return Producer
     */
    public function getProducer() : Producer
    {
        return $this->producer;
    }
}