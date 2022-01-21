<?php

declare(strict_types=1);

namespace JeanBeru\PipelineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('pipeline');

        $treeBuilder->getRootNode()
            ->fixXmlConfig('pipeline')
            ->children()
                ->arrayNode('pipelines')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                    ->children()
                        ->scalarNode('processor')->defaultNull()->end()
                        ->arrayNode('stages')
                            ->requiresAtLeastOneElement()
                            ->scalarPrototype()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
