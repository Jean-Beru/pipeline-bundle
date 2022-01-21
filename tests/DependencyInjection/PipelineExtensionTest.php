<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\Tests\DependencyInjection;

use JeanBeru\PipelineBundle\DependencyInjection\PipelineExtension;
use League\Pipeline\PipelineInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PipelineExtensionTest extends TestCase
{
    public function testLoadProcessor(): void
    {
        $container = $this->createContainer([
            'pipelines' => [
                'custom_pipeline' => [
                    'processor' => 'custom_processor',
                    'stages' => ['custom_stage_1', 'custom_stage_2'],
                ],
            ],
        ]);

        $definitionProcessor = $container->getDefinition('jean_beru_pipeline.pipeline.custom_pipeline')->getArgument(1);

        self::assertInstanceOf(Reference::class, $definitionProcessor);
        self::assertSame('custom_processor', (string) $definitionProcessor);
    }

    public function testLoadStages(): void
    {
        $container = $this->createContainer([
            'pipelines' => [
                'pipeline_one' => [
                    'stages' => ['stage_1.1', 'stage_1.2', 'stage_1.3', 'stage_1.4'],
                ],
                'pipeline_two' => [
                    'stages' => ['stage_2.1', 'stage_2.2', 'stage_2.3'],
                ],
            ],
        ]);

        $this->assertPipelineDefinition($container, 'jean_beru_pipeline.pipeline.pipeline_one', PipelineInterface::class. ' $pipelineOnePipeline', [
            'stage_1.1',
            'stage_1.2',
            'stage_1.3',
            'stage_1.4',
        ]);
        $this->assertPipelineDefinition($container, 'jean_beru_pipeline.pipeline.pipeline_two', PipelineInterface::class. ' $pipelineTwoPipeline', [
            'stage_2.1',
            'stage_2.2',
            'stage_2.3',
        ]);
    }

    /**
     * @param array<string> $stages
     */
    private function assertPipelineDefinition(ContainerBuilder $container, string $id, string $alias, array $stages): void
    {
        self::assertTrue($container->hasDefinition($id), "Definition \"$id\" not found.");
        self::assertTrue($container->hasAlias($alias), "Alias \"$alias\" not found.");
        self::assertSame($id, (string) $container->getAlias($alias));

        $definitionStages = $container->getDefinition($id)->getArgument(0);
        self::assertIsIterable($definitionStages);

        $argumentCount = 0;
        foreach ($definitionStages as $definitionStage) {
            self::assertInstanceOf(Reference::class, $definitionStage);
            self::assertSame($stages[$argumentCount], (string) $definitionStage);
            ++$argumentCount;
        }
        self::assertSame(count($stages), $argumentCount);
    }

    /**
     * @param array<mixed> $config
     */
    private function createContainer(array $config): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new PipelineExtension());
        $container->loadFromExtension('pipeline', $config);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
