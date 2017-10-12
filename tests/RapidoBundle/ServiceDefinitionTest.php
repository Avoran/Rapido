<?php

namespace Avoran\RapidoBundle;

use Avoran\RapidoAdapter\DoctrineDbalStorage\DoctrineDbalStorageWriter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceDefinitionTest extends KernelTestCase
{
    /** @var ContainerInterface */
    private $container;

    protected function setUp()
    {
        $this->container = self::bootKernel()->getContainer();
    }

    /** @test */
    public function kernel_should_boot()
    {
        $this->assertInstanceOf(DoctrineDbalStorageWriter::class, $this->container->get('rapido.storage_writer'));
    }
}
