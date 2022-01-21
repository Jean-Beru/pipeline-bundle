<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\DependencyInjection;

use League\Pipeline\PipelineInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class PipelineExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('pipeline.php');

        if ($container::willBeAvailable('symfony/event-dispatcher', EventDispatcherInterface::class, ['jean-beru/pipeline-bundle'])) {
            $loader->load('event_dispatcher.php');
        }

        /** @var Configuration $configuration */
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $pipelineFactory = new Reference('jean_beru_pipeline.pipeline_factory');
        foreach ($config['pipelines'] ?? [] as $name => $pipelineConfiguration) {
            $this->registerPipeline($container, $pipelineFactory, $name, $pipelineConfiguration);
        }
    }

    /**
     * @param array{stages: array<string>, processor: string} $configuration
     */
    private function registerPipeline(ContainerBuilder $container, Reference $factory, string $name, array $configuration): void
    {
        ['stages' => $stages, 'processor' => $processor] = $configuration;

        $definition = new Definition(PipelineInterface::class);
        $definition->setFactory($factory);
        $definition->addArgument(array_map(static fn (string $id) => new Reference($id), $stages));
        if ($processor) {
            $definition->addArgument(new Reference($processor));
        }

        $serviceId = "jean_beru_pipeline.pipeline.${name}";
        $container->setDefinition($serviceId, $definition);
        $container->setAlias(PipelineInterface::class, $serviceId);
        $container->registerAliasForArgument($serviceId, PipelineInterface::class, "${name}Pipeline");
    }
}
