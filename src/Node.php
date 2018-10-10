<?php

namespace Scaleplan\Kafka;

/**
 * Class Node
 *
 * @package Scaleplan\Event\Kafka
 */
class Node
{
    protected $host;

    protected $id;

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }
}