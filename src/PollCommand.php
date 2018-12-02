<?php

namespace Scaleplan\Kafka;

use Scaleplan\Console\AbstractCommand;
use function Scaleplan\Event\dispatch;

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

    public const ERROR_EVENT = 'kafka_error';

    /**
     * @var Kafka
     */
    protected $kafka;

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
            dispatch(static::ERROR_EVENT, [
                'errorMessage' => rd_kafka_err2str($err),
                'reason'       => $reason,
                'kafka'        => $kafka,
            ]);
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