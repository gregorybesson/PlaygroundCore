<?php

namespace PlaygroundCore\Service;

use Laminas\ServiceManager\ServiceManager;
use Laminas\EventManager\EventManagerAwareTrait;
use PlaygroundCore\Options\ModuleOptions;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Website
{
    use EventManagerAwareTrait;

    /**
     * @var websiteMapper
     */
    protected $websiteMapper;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;

    /**
     *
     * @var ServiceManager
     */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

    /**
     * getWebsiteMapper
     *
     * @return websiteMapper
     */
    public function getWebsiteMapper()
    {
        if (null === $this->websiteMapper) {
            $this->websiteMapper = $this->serviceLocator->get('playgroundcore_website_mapper');
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
            $this->setOptions($this->serviceLocator->get('playgroundcore_module_options'));
        }

        return $this->options;
    }
}
