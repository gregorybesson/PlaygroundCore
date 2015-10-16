<?php

namespace PlaygroundCore\Service;

use PlaygroundCore\Entity\Website as WebsiteEntity;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\NotEmpty;
use ZfcBase\EventManager\EventProvider;
use PlaygroundCore\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Zend\Stdlib\ErrorHandler;

class Website extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var websiteMapper
     */
    protected $websiteMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;


    /**
     * getWebsiteMapper
     *
     * @return websiteMapper
     */
    public function getWebsiteMapper()
    {
        if (null === $this->websiteMapper) {
            $this->websiteMapper = $this->getServiceManager()->get('playgroundcore_website_mapper');
        }

        return $this->websiteMapper;
    }

    /**
     * setWebsiteMapper
     * @param  Mapper/Website $websiteMapper
     *
     * @return PlaygroundCore\Entity\WebsiteMapper websiteMapper
     */
    public function setWebsiteMapper($websiteMapper)
    {
        $this->websiteMapper = $websiteMapper;

        return $this;
    }

    /**
     * setOptions
     * @param  ModuleOptions $options
     *
     * @return PlaygroundCore\Service\Website $this
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * getOptions
     *
     * @return ModuleOptions $optins
     */
    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceManager()->get('playgroundcore_module_options'));
        }

        return $this->options;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}
