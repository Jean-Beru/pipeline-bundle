<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\Tests\DependencyInjection;

use JeanBeru\PipelineBundle\DependencyInjection\PipelineExtension;
use League\Pipeline\PipelineInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class PipelineExtensionTest extends TestCase
{
    public function testItLoadsProcessor(): void
    {
        $container = $this->createContainerFromFile('processor');

        $definitionProcessor = $container->getDefinition('jeanberu_pipeline.pipeline.custom_pipeline')->getArgument(1);

        $this->assertInstanceOf(Reference::class, $definitionProcessor);
        $this->assertSame('custom_processor', (string) $definitionProcessor);
    }

    public function testItLoadsMultiplePipelines(): void
    {
        $container = $this->createContainerFromFile('multiple');

        $this->assertPipelineDefinition($container, 'jeanberu_pipeline.pipeline.pipeline_one', PipelineInterface::class. ' $pipelineOnePipeline', [
            'stage_1.1',
            'stage_1.2',
            'stage_1.3',
            'stage_1.4',
        ]);
        $this->assertPipelineDefinition($container, 'jeanberu_pipeline.pipeline.pipeline_two', PipelineInterface::class. ' $pipelineTwoPipeline', [
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
        $this->assertTrue($container->hasDefinition($id), "Definition \"$id\" not found.");
        $this->assertTrue($container->hasAlias($alias), "Alias \"$alias\" not found.");
        $this->assertSame($id, (string) $container->getAlias($alias));

        $definitionStages = $container->getDefinition($id)->getArgument(0);
        $this->assertIsIterable($definitionStages);

        $argumentCount = 0;
        foreach ($definitionStages as $definitionStage) {
            $this->assertInstanceOf(Reference::class, $definitionStage);
            $this->assertSame($stages[$argumentCount], (string) $definitionStage);
            ++$argumentCount;
        }
        $this->assertSame(count($stages), $argumentCount);
    }

    private function createContainerFromFile(string $file): ContainerBuilder
    {

        $container = new ContainerBuilder();
        $container->registerExtension(new PipelineExtension());

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Fixtures/configuration'));
        $loader->load($file.'.yaml');

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
