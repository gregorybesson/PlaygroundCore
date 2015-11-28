<?php

namespace PlaygroundCore\Service\Factory;

use Services_Twilio;
use PlaygroundCore\Options\ModuleOptions;
use Zend\ServiceManager\Exception\InvalidArgumentException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

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
    
    /**
     * Generates the Item controller
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Services_Twilio
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        /**
         * @var ServiceManager $serviceManager
         * @var ModuleOptions $options
         */
        $options = $serviceManager->get('playgroundcore_module_options');
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
