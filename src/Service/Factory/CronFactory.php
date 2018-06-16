<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Cron;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CronFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Service\Cron
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Cron($locator);

        return $service;
    }
}
