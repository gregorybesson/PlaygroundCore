<?php
namespace PlaygroundCore\Service;

use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * main class
 */
class Recaptcha extends EventProvider
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
     * This method calls Google ReCaptcha.
     * @param  unknown_type $url
     * @return unknown
     */
    public function recaptcha($response, $ipClient = null)
    {
        if ($this->getOptions()->getGRecaptchaKey()) {
            $client = new \Zend\Http\Client($this->getOptions()->getGRecaptchaUrl());
            $client->setParameterPost(array(
                'secret'  => $this->getOptions()->getGRecaptchaKey(),
                'response' => $response,
                'remoteip'   => $ipClient,
            ));
            $client->setMethod(\Zend\Http\Request::METHOD_POST);
    
            $result = $client->send();
            if ($result) {
                $jsonResult = \Zend\Json\Json::decode($result->getBody());
                if ($jsonResult->success) {
                    return true;
                }
            }
        }

        return false;
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
