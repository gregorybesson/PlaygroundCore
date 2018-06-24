<?php

namespace PlaygroundCore\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CountryName extends AbstractHelper
{
    protected $service = null;

    public function __construct(\PlaygroundCore\Service\Country $service)
    {
        $this->service = $service;
    }

    public function __invoke($code, $locale = null)
    {
        if (empty($locale)) {
            $locale = $this->service->getServiceManager()->get('MvcTranslator')->getLocale();
        }

        return $this->service->getCountry($code, $locale);
    }
}
