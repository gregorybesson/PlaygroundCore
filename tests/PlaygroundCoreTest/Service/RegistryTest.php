<?php

namespace PlaygroundCoreTest\Service;

use PlaygroundCoreTest\Bootstrap;
use PlaygroundCore\Service\Registry;

class RegistryTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->sm = Bootstrap::getServiceManager();
    }
    
    public function testInstance()
    {
        $instance = Registry::getInstance();
        $this->assertInstanceOf('PlaygroundCore\Service\Registry', $instance);
    }
    
    public function testSingleton()
    {
        $instance1 = Registry::getInstance();
        $instance2 = Registry::getInstance();
        $this->assertSame($instance1, $instance2);
    }
    
    public function testDestroy()
    {
        $instance1 = Registry::getInstance();
        Registry::destroy();
        $instance2 = Registry::getInstance();
        $this->assertNotSame($instance1, $instance2);
    }
    
    public function testCronRegistry()
    {
        Registry::register(
            'test',
            '* * * * *',
            array($this, 'dummy'),
            array()
        );
    
        $expectedCronRegistry = array(
            'test' => array(
                'frequency' => '* * * * *',
                'callback'  => array($this, 'dummy'),
                'args'      => array(),
            ),
        );
        $cronRegistry = Registry::getCronRegistry();
    
        $this->assertSame($expectedCronRegistry, $cronRegistry);
    }
    
    //dummy function to act as cron job
    public function dummy()
    {
    }
}
