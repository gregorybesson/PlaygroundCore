<?php

namespace PlaygroundCoreTest\Entity;

use PlaygroundCoreTest\Bootstrap;
use \PlaygroundCore\Entity\Locale as LocaleEntity;

class LocaleTest extends \PHPUnit_Framework_TestCase
{

    protected $locale;
    protected $time;

    public function setUp()
    {
        $this->time = time();

        $this->locale = array(
            'name' => 'French',
            'locale' => 'fr_FR',
            'active_back'  => '0',
            'active_front' => '0',
        );

        parent::setUp();
    }

    public function testPopulate() 
    {
        $localeEntity = new LocaleEntity;
        $localeEntity->populate($this->locale);
        $this->assertEquals($this->locale["name"], $localeEntity->getName());
        $this->assertEquals($this->locale["locale"], $localeEntity->getLocale());
        $this->assertEquals($this->locale["active_back"], $localeEntity->getActiveBack());
        $this->assertEquals($this->locale["active_front"], $localeEntity->getActiveFront());  
    }

    public function testTimestampables()
    {
        $localeEntity = new LocaleEntity;
        $localeEntity->populate($this->locale);
        $localeEntity->setCreatedAt($this->time);
        $localeEntity->setUpdatedAt($this->time);

        $this->assertEquals($this->time, $localeEntity->getCreatedAt());
        $this->assertEquals($this->time, $localeEntity->getUpdatedAt());
    }


    public function testGetFlag()
    {

        $localeEntity = new LocaleEntity;
        $localeEntity->populate($this->locale);

        $this->assertEquals("/lib/images/flag/fr", $localeEntity->getFlag());
    }

    public function testSetId()
    {
        $id = 1;
        $localeEntity = new LocaleEntity;
        $localeEntity->setId($id);
        $this->assertEquals($id, $localeEntity->getId());
    }

}