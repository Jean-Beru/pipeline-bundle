<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\Event;

abstract class AbstractProcessorEvent
{
    /**
     * @var mixed $payload
     */
    private $payload;

    /**
     * @param mixed $payload
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
