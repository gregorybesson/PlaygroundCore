<?php

namespace PlaygroundCoreTest\Service;

use PlaygroundCoreTest\Bootstrap;
use PlaygroundCore\Service\Registry;

class LocaleTest extends \PHPUnit\Framework\TestCase
{
    protected $traceError = true;
    
    protected $cronData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sm = Bootstrap::getServiceManager();
    }

    public function testMapper()
    {
        $service = $this->sm->get('playgroundcore_locale_service');

        $mapper = $service->getLocaleMapper();
        $this->assertEquals(get_class($mapper), 'PlaygroundCore\Mapper\Locale');
       
        $mapper = $service->setLocaleMapper($this->sm->get('playgroundcore_locale_mapper'));
        $mapper = $service->getLocaleMapper();
        $this->assertEquals(get_class($mapper), 'PlaygroundCore\Mapper\Locale');
    }

    public function testOption()
    {
        $service = $this->sm->get('playgroundcore_locale_service');

        $mapper = $service->getOptions();
        $this->assertEquals(get_class($mapper), 'PlaygroundCore\Options\ModuleOptions');
       
        $mapper = $service->setOptions($this->sm->get('playgroundcore_module_options'));
        $mapper = $service->getOptions();
        $this->assertEquals(get_class($mapper), 'PlaygroundCore\Options\ModuleOptions');
    }
}
