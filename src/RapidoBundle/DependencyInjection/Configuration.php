<?php

namespace Avoran\RapidoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('rapido');

        $rootNode
            ->children()
                ->scalarNode('table_name_prefix')
                    ->defaultValue('read_model_')
                ->end()
                ->scalarNode('id_column_name')
                    ->defaultValue('id')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}