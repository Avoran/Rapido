<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new Avoran\RapidoBundle\RapidoBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle()
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        new \PDO('sqlite:' . __DIR__ . '/sqlite.db');
        $loader->load(__DIR__ . '/config.yml');
    }
}
