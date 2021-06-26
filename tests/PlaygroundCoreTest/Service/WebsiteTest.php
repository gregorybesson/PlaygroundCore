<?php

namespace PlaygroundCoreTest\Service;

use PlaygroundCoreTest\Bootstrap;
use PlaygroundCore\Service\Registry;

class WebsiteTest extends \PHPUnit\Framework\TestCase
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
        $service = $this->sm->get('playgroundcore_website_service');

        $mapper = $service->getWebsiteMapper();
        $this->assertEquals(get_class($mapper), 'PlaygroundCore\Mapper\Website');
       
        $mapper = $service->setWebsiteMapper($this->sm->get('playgroundcore_website_mapper'));
        $mapper = $service->getWebsiteMapper();
        $this->assertEquals(get_class($mapper), 'PlaygroundCore\Mapper\Website');
    }

    public function testOption()
    {
        $service = $this->sm->get('playgroundcore_website_service');

        $mapper = $service->getOptions();
        $this->assertEquals(get_class($mapper), 'PlaygroundCore\Options\ModuleOptions');
       
        $mapper = $service->setOptions($this->sm->get('playgroundcore_module_options'));
        $mapper = $service->getOptions();
        $this->assertEquals(get_class($mapper), 'PlaygroundCore\Options\ModuleOptions');
    }
}
