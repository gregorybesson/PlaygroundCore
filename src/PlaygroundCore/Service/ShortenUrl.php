<?php
namespace PlaygroundCore\Service;

use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * main class
 */
class ShortenUrl extends EventProvider
{
    /**
     * @var ModuleOptions
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
     * This method call Bit.ly to shorten a given URL.
     * @param  unknown_type $url
     * @return unknown
     */
    public function shortenUrl($url)
    {
        if ($this->getOptions()->getBitlyApiKey() && $this->getOptions()->getBitlyUsername()) {
            $client = new \Zend\Http\Client($this->getOptions()->getBitlyUrl());
            $client->setParameterGet(array(
                'format'  => 'json',
                'longUrl' => $url,
                'login'   => $this->getOptions()->getBitlyUsername(),
                'apiKey'  => $this->getOptions()->getBitlyApiKey(),
            ));
    
            $result = $client->send();
            if ($result) {
                $jsonResult = \Zend\Json\Json::decode($result->getBody());
                if ($jsonResult->status_code == 200) {
                    return $jsonResult->data->url;
                }
            }
        }

        return $url;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions($this->serviceLocator->get('playgroundcore_module_options'));
        }

        return $this->options;
    }
}
