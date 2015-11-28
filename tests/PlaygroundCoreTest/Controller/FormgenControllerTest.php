<?php

namespace PlaygroundCoreTest\Controller\Frontend;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class FormgenControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../TestConfig.php'
        );

        parent::setUp();
    }

    public function testViewAction()
    {
        $this->assertTrue(true);
    }
}
