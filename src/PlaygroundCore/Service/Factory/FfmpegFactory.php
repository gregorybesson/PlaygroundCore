<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Ffmpeg;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FfmpegFactory implements FactoryInterface
{
    /**
    * @param ServiceLocatorInterface $locator
    * @return \PlaygroundCore\Service\Ffmpeg
    */
    public function createService(ServiceLocatorInterface $locator)
    {
        $service = new Ffmpeg($locator);

        return $service;
    }
}
