<?php

namespace Scaleplan\Kafka;

use Scaleplan\InitTrait\InitTrait;
use Scaleplan\Kafka\Exceptions\ConfigBrokerProtoMismatch;

/**
 * Class Config
 *
 * @package Scaleplan\Event\Kafka
 */
class Config
{
    use InitTrait;

    public const PROTO_PLAINTEXT      = 'PLAINTEXT';
    public const PROTO_SSL            = 'SSL';
    public const PROTO_SASL           = 'SASL';
    public const PROTO_SASL_PLAINTEXT = 'SASL_PLAINTEXT';

    public const AVAILABLE_PROTO = [
        self::PROTO_PLAINTEXT,
        self::PROTO_SSL,
        self::PROTO_SASL,
        self::PROTO_SASL_PLAINTEXT,
    ];

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var Node[]
     */
    protected $consumers;

    /**
     * @var array
     */
    protected $brokers;

    /**
     * Config constructor.
     *
     * @param array $settings
     *
     * @throws \ReflectionException
     */
    public function __construct(array $settings)
    {
        $this->initObject($settings);
    }

    /**
     * @param string $host
     * @param int $port
     * @param string $proto
     * @return string
     * @throws ConfigBrokerProtoMismatch
     */
    protected static function structBroker(string $host, int $port = 0, string $proto = '') : string
    {
        $bro = $host;
        if ($port) {
            $bro .= ":$port";
        }

        if ($proto && !\in_array($proto, static::AVAILABLE_PROTO, true)) {
            throw new ConfigBrokerProtoMismatch();
        }

        if ($proto) {
            $bro = "$proto://$bro";
        }

        return $bro;
    }

    /**
     * @param string $host
     * @param int $port
     * @param string $proto
     *
     * @throws ConfigBrokerProtoMismatch
     */
    public function addBroker(string $host, int $port = 0, string $proto = '') : void
    {
        $this->brokers[] = self::structBroker($host, $port, $proto);
    }

    /**
     * @param string $host
     * @param int $port
     * @param string $proto
     *
     * @throws ConfigBrokerProtoMismatch
     */
    public function removeBroker(string $host, int $port = 0, string $proto = '') : void
    {
        $brokerName = self::structBroker($host, $port, $proto);
        $key = \array_search($brokerName, $this->brokers, true);
        if ($key !== false) {
            unset($this->brokers[$key]);
        }
    }

    /**
     * @return array
     */
    public function getBrokers() : array
    {
        return $this->brokers;
    }

    /**
     * @return Node[]
     */
    public function getConsumers() : array
    {
        return $this->consumers;
    }

    /**
     * @return int
     */
    public function getTimeout() : int
    {
        return $this->timeout;
    }
}