<?php

namespace Scaleplan\Kafka;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Producer;
use RdKafka\ProducerTopic;
use RdKafka\TopicConf;
use function Scaleplan\Helpers\get_required_env;
use Scaleplan\Kafka\Exceptions\ConsumeException;
use Scaleplan\Kafka\Exceptions\ConsumeTimedOutException;
use function Scaleplan\Helpers\get_env;
use Scaleplan\Kafka\Interfaces\KafkaInterface;

/**
 * Class Kafka
 *
 * @package Scaleplan\Event
 */
class Kafka implements KafkaInterface
{
    public const LOG_LEVEL = LOG_WARNING;
    public const TIMEOUT   = 1e4;

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
     * @var ProducerTopic
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
     *
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    protected function __construct(array $consumerTopics = null)
    {
        $this->consumerTopics = $consumerTopics ?? array_map('trim', explode(',', get_env('KAFKA_CONSUMER_TOPICS')));
        $this->brokers = get_required_env('KAFKA_BROKERS');
        $this->timeout = (int)(get_env('KAFKA_TIMEOUT') ?? static::TIMEOUT);
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
     * @param string $data
     */
    public function produce(string $topicName, string $data) : void
    {
        $this->getProducerTopic($topicName)->produce(RD_KAFKA_PARTITION_UA, 0, $data);
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
