<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\Factory;

use League\Pipeline\PipelineBuilder;
use League\Pipeline\PipelineInterface;
use League\Pipeline\ProcessorInterface;
use League\Pipeline\StageInterface;

final class PipelineFactory
{
    /**
     * @param iterable<callable|StageInterface> $stages
     */
    public function __invoke(iterable $stages, ProcessorInterface $processor = null): PipelineInterface
    {
        $builder = new PipelineBuilder();
        foreach ($stages as $stage) {
            $builder->add($stage);
        }

        return $builder->build($processor);
    }
}
