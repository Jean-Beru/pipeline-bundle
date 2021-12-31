<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\DependencyInjection;

use JeanBeru\PipelineBundle\Factory\PipelineFactory;
use League\Pipeline\PipelineInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class PipelineExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var Configuration $configuration */
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $container->register('jean_beru_pipeline.pipeline_factory', PipelineFactory::class);
        $pipelineFactory = new Reference('jean_beru_pipeline.pipeline_factory');


        foreach ($config['pipelines'] ?? [] as $name => $stages) {
            $definition = new Definition(PipelineInterface::class);
            $definition->setFactory($pipelineFactory);
            $definition->addArgument(array_map(static fn (string $id) => new Reference($id), $stages));

            $serviceId = 'jean_beru_pipeline.pipeline.'.$name;
            $serviceArgument = $name.'Pipeline';
            $container->setDefinition($serviceId, $definition);
            $container->setAlias(PipelineInterface::class, $serviceId);
            $container->registerAliasForArgument($serviceId, PipelineInterface::class, $serviceArgument);
        }
    }
}
