<?php
namespace PlaygroundCore\Service;

use Laminas\ServiceManager\ServiceManager;
use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * main class
 */
class ShortenUrl
{
    use EventManagerAwareTrait;

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
            $client = new \Laminas\Http\Client($this->getOptions()->getBitlyUrl());
            $client->setParameterGet(array(
                'format'  => 'json',
                'longUrl' => $url,
                'login'   => $this->getOptions()->getBitlyUsername(),
                'apiKey'  => $this->getOptions()->getBitlyApiKey(),
            ));
    
            $result = $client->send();
            if ($result) {
                $jsonResult = \Laminas\Json\Json::decode($result->getBody());
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
