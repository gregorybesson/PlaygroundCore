<?php

namespace PlaygroundCoreTest\Controller\Frontend;

use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class FormgenControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    protected function setUp(): void
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
