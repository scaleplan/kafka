<?php

namespace Scaleplan\Kafka;

use Scaleplan\Console\AbstractCommand;
use function Scaleplan\Event\dispatch;
use Scaleplan\Kafka\Events\KafkaErrorEvent;

/**
 * Class PollCommand
 *
 * @package Scaleplan\Kafka
 */
class PollCommand extends AbstractCommand
{
    /**
     * Daemon command run timeout (in microseconds)
     */
    public const DAEMON_TIMEOUT = 500;

    /**
     * @var Kafka
     */
    protected $kafka;

    /**
     * @var string
     */
    protected $lastReason;

    /**
     * @var string
     */
    protected $lastError;

    /**
     * PollCommand constructor.
     *
     * @throws \Scaleplan\Console\Exceptions\CommandSignatureIsEmptyException
     */
    public function __construct()
    {
        parent::__construct();

        $this->kafka = Kafka::getInstance();
        $conf = $this->kafka->getConf();
        $conf->setErrorCb(function ($kafka, $err, $reason) {
            $this->lastError = rd_kafka_err2str($err);
            $this->lastReason = $reason;
            dispatch(KafkaErrorEvent::class, ['kafkaPoller' => $this]);
        });
    }

    /**
     * Polls for Kafka events
     */
    public function run() : void
    {
        $this->kafka->getProducer()->poll(0);
    }
}
