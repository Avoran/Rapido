<?php

namespace Avoran\RapidoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('rapido');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('table_name_prefix')
                    ->defaultValue('read_model_')
                ->end()
                ->scalarNode('table_name_suffix')
                    ->defaultValue('_snapshot')
                ->end()
                ->scalarNode('id_column_name')
                    ->defaultValue('id')
                ->end()
                ->scalarNode('database_connection')
                    ->defaultValue('database_connection')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
