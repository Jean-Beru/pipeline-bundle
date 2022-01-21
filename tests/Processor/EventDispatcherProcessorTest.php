<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\Tests\Processor;

use JeanBeru\PipelineBundle\Event\AfterProcessorEvent;
use JeanBeru\PipelineBundle\Event\AfterStageEvent;
use JeanBeru\PipelineBundle\Event\BeforeProcessorEvent;
use JeanBeru\PipelineBundle\Event\BeforeStageEvent;
use JeanBeru\PipelineBundle\Processor\EventDispatcherProcessor;
use League\Pipeline\StageInterface;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventDispatcherProcessorTest extends TestCase
{
    public function testProcess(): void
    {
        $stageAdd10 = $this->createMock(StageInterface::class);
        $stageAdd10->expects(self::once())->method('__invoke')->willReturnCallback(static fn (int $payload) => $payload + 10);
        $stageDiv10 = static fn (int $payload) => $payload / 10;
        $stageIntVal = 'intval';

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects(self::exactly(8))
            ->method('dispatch')
            ->withConsecutive(
                [self::equalTo(new BeforeProcessorEvent(42))],
                [self::equalTo(new BeforeStageEvent(\get_class($stageAdd10), 42))],
                [self::equalTo(new AfterStageEvent(\get_class($stageAdd10), 52))],
                [self::equalTo(new BeforeStageEvent(\Closure::class, 52))],
                [self::equalTo(new AfterStageEvent(\Closure::class, 5.2))],
                [self::equalTo(new BeforeStageEvent('intval', 5.2))],
                [self::equalTo(new AfterStageEvent('intval', 5))],
                [self::equalTo(new AfterProcessorEvent(5))],
            )
        ;

        $processor = new EventDispatcherProcessor($eventDispatcher);

        $this->assertSame(5, $processor->process(42, $stageAdd10, $stageDiv10, $stageIntVal));
    }
}
