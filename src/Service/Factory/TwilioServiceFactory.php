<?php

namespace PlaygroundCore\Service\Factory;

use Services_Twilio;
use PlaygroundCore\Options\ModuleOptions;
use Laminas\ServiceManager\Exception\InvalidArgumentException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class TwilioServiceFactory
 *
 * @package PlaygroundCore\Service\Factory
 */
class TwilioServiceFactory implements FactoryInterface
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
        $TwilioOptions = $options->getTwilio();
        
        if (!isset($TwilioOptions['sid']) || $TwilioOptions['sid'] === '') {
            throw new InvalidArgumentException('No Twilio SID configured');
        }

        if (!isset($TwilioOptions['token']) || $TwilioOptions['token'] === '') {
            throw new InvalidArgumentException('No Twilio Token configured');
        }

        $service = new Services_Twilio(
            $TwilioOptions['sid'],
            $TwilioOptions['token']
        );

        return $service;
    }
}
