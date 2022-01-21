<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\Event;

class AbstractStageEvent extends AbstractProcessorEvent
{
    private string $stage;

    /**
     * @param mixed $payload
     */
    public function __construct(string $stage, $payload)
    {
        parent::__construct($payload);
        $this->stage = $stage;
    }

    public function getStage(): string
    {
        return $this->stage;
    }
}
