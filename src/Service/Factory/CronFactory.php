<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Cron;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class CronFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Cron($container);

        return $service;
    }
}
