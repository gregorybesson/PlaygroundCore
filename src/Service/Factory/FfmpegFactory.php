<?php
namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Service\Ffmpeg;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FfmpegFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        $service = new Ffmpeg($container);

        return $service;
    }
}
