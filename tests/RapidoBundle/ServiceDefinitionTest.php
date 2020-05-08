<?php

namespace Avoran\RapidoBundle;

use Avoran\RapidoAdapter\DoctrineDbalStorage\DoctrineDbalStorageWriter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ServiceDefinitionTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    /** @test */
    public function kernel_should_boot()
    {
        $this->assertInstanceOf(DoctrineDbalStorageWriter::class, self::$kernel->getContainer()->get('rapido.storage_writer'));
    }
}
