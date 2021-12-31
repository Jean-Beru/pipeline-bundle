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
    public function testLoad(): void
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new PipelineExtension());
        $container->loadFromExtension('pipeline', [
            'pipelines' => [
                'update_stock' => [
                    'retrieve',
                    'update_stocks',
                    'update_status',
                    'persist',
                ],
                'import_users' => [
                    'retrieve',
                    'import',
                    'persist',
                ],
            ],
        ]);
        $this->compileContainer($container);

        $this->assertDefinition($container, 'jean_beru_pipeline.pipeline.update_stock', PipelineInterface::class. ' $updateStockPipeline', [
            'retrieve',
            'update_stocks',
            'update_status',
            'persist',
        ]);
        $this->assertDefinition($container, 'jean_beru_pipeline.pipeline.import_users', PipelineInterface::class. ' $importUsersPipeline', [
            'retrieve',
            'import',
            'persist',
        ]);
    }

    /**
     * @param array<string> $stages
     */
    private function assertDefinition(ContainerBuilder $container, string $id, string $alias, array $stages): void
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

    private function compileContainer(ContainerBuilder $container): void
    {
        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();
    }
}
