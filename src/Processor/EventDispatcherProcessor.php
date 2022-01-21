<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\Processor;

use JeanBeru\PipelineBundle\Event\AfterProcessorEvent;
use JeanBeru\PipelineBundle\Event\AfterStageEvent;
use JeanBeru\PipelineBundle\Event\BeforeProcessorEvent;
use JeanBeru\PipelineBundle\Event\BeforeStageEvent;
use League\Pipeline\ProcessorInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventDispatcherProcessor implements ProcessorInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param mixed $payload
     *
     * @return mixed
     */
    public function process($payload, callable ...$stages)
    {
        $this->eventDispatcher->dispatch(new BeforeProcessorEvent($payload));

        foreach ($stages as $stage) {
            $stageName = $this->getStageName($stage);

            $this->eventDispatcher->dispatch(new BeforeStageEvent($stageName, $payload));
            $payload = $stage($payload);
            $this->eventDispatcher->dispatch(new AfterStageEvent($stageName, $payload));
        }

        $this->eventDispatcher->dispatch(new AfterProcessorEvent($payload));

        return $payload;
    }

    private function getStageName(callable $stage): string
    {
        if (is_string($stage)) {
            return $stage;
        }

        if (is_object($stage)) {
            return \get_class($stage);
        }

        return 'callable';
    }
}
