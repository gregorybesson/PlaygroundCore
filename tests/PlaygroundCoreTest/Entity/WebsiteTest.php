<?php

namespace PlaygroundCoreTest\Entity;

use PlaygroundCoreTest\Bootstrap;
use \PlaygroundCore\Entity\Website as WebsiteEntity;

class WebsiteTest extends \PHPUnit\Framework\TestCase
{

    protected $website;

    protected function setUp(): void
    {
        $this->time = time();

        $this->website = array(
            'name' => 'Frenchfff',
            'code' => 'frff',
            'default' => '0',
            'active' => '0',
            'locales' => null,
        );

        parent::setUp();
    }

    public function testPopulate()
    {
        $websiteEntity = new WebsiteEntity;
        $websiteEntity->populate($this->website);
        $this->assertEquals($this->website["name"], $websiteEntity->getName());
        $this->assertEquals($this->website["code"], $websiteEntity->getCode());
        $this->assertEquals($this->website["default"], $websiteEntity->getDefault());
        $this->assertEquals($this->website["locales"], $websiteEntity->getLocales());
        $this->assertEquals($this->website["active"], $websiteEntity->getActive());

        unset($websiteEntity);
    }

    public function testTimestampables()
    {
        $websiteEntity = new WebsiteEntity;
        $websiteEntity->populate($this->website);
        $websiteEntity->setCreatedAt($this->time);
        $websiteEntity->setUpdatedAt($this->time);

        $this->assertEquals($this->time, $websiteEntity->getCreatedAt());
        $this->assertEquals($this->time, $websiteEntity->getUpdatedAt());

        unset($websiteEntity);
    }

    public function testGetLocales()
    {
        $websiteEntity = new WebsiteEntity;
        $websiteEntity->populate($this->website);
        $websiteEntity->setLocales(null);

        $this->assertEquals(null, $websiteEntity->getLocales());

        unset($websiteEntity);
    }

    public function testGetFalg()
    {
        $websiteEntity = new WebsiteEntity;
        $websiteEntity->populate($this->website);
        
        $this->assertEquals('/lib/images/flag/frff', $websiteEntity->getFlag());

        unset($websiteEntity);
    }
}
