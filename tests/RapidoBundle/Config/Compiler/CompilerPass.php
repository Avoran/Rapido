<?php

namespace Avoran\RapidoBundle\Config\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('rapido_adapter.doctrine_dbal_storage.schema_manager');
        $definition->setClass('Doctrine\DBAL\Schema\SqliteSchemaManager');
    }
}
