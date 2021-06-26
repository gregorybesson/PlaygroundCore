<?php

namespace PlaygroundCore\Service\Factory;

use PlaygroundCore\Options\ModuleOptions;
use Laminas\ServiceManager\Exception\InvalidArgumentException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class PhpvideotoolkitServiceFactory
 *
 * @package PlaygroundCore\Service\Factory
 */
class PhpvideotoolkitServiceFactory implements FactoryInterface
{
    
    /**
     * @var PlaygroundCoreOptionsInterface
     */
    protected $options;
    
    public function __invoke(ContainerInterface $container, $requestedName, $options = null)
    {
        /**
         * @var ServiceManager $container
         * @var ModuleOptions $options
         */
        $options = $container->get('playgroundcore_module_options');
        $phpvideotoolkitOptions = $options->getPhpvideotoolkit();
        
        if (!isset($phpvideotoolkitOptions['ffmpeg']) || $phpvideotoolkitOptions['ffmpeg'] === '') {
            throw new \Laminas\ServiceManager\Exception\InvalidArgumentException(
                'No phpvideotoolkit configuration found'
            );
        }

        try {
            $config = new \PHPVideoToolkit\Config($phpvideotoolkitOptions);
        } catch (\PHPVideoToolkit\Exception $e) {
            throw new \Laminas\ServiceManager\Exception\InvalidArgumentException(
                'phpvideotoolkit error during configuration load'
            );
        }
        
        try {
            $service = new \PHPVideoToolkit\FfmpegProcess('ffmpeg', $config);
        } catch (\PHPVideoToolkit\Exception $e) {
            \PHPVideoToolkit\Trace::vars($e->getMessage());
            \PHPVideoToolkit\Trace::vars($e);
            throw new \Laminas\ServiceManager\Exception\InvalidArgumentException(
                'phpvideotoolkit process creation error'
            );
        }

        return $service;
    }
}
