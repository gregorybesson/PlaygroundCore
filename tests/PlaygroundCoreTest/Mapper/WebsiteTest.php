<?php

namespace PlaygroundCoreTest\Mapper;

use PlaygroundCoreTest\Bootstrap;
use \PlaygroundCore\Entity\Website as websiteEntity;
use \PlaygroundCore\Entity\Locale as localeEntity;

class WebsiteTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;

    protected $localeMapper;

    protected $websiteMapper;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->sm->get('doctrine.entitymanager.orm_default');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
        parent::setUp();
    }

    public function testInsert()
    {

        $websites = $this->getWebsiteMapper()->findAll();
        
        foreach ($websites as $website) {
           $this->getWebsiteMapper()->remove($website); 
        }
        
        $locale = new localeEntity();
        $locale->setName('French');
        $locale->setLocale('fr_FR');
        $locale->setActiveFront(1);
        $locale->setActiveBack(1);
        $this->getLocaleMapper()->insert($locale);


        $website = new websiteEntity();
        $website->setName('France');
        $website->setCode('FR');
        
        $website->setActive(true);
        $website->setDefault(0);
        $website->addLocale($locale);

        $this->getWebsiteMapper()->insert($website);

        $website = new websiteEntity();
        $website->setName('Italy');
        $website->setCode('IT');
        
        $website->setActive(true);
        $website->setDefault(0);
        $website->addLocale($locale);

        $this->getWebsiteMapper()->insert($website);


        $websites = $this->getWebsiteMapper()->findAll();
        $this->assertEquals(count($websites), 2);

 
    }

    public function testFindBy(){

        $locale = new localeEntity();
        $locale->setName('French');
        $locale->setLocale('fr_FR');
        $locale->setActiveFront(1);
        $locale->setActiveBack(1);
        $this->getLocaleMapper()->insert($locale);


        $website = new websiteEntity();
        $website->setName('France');
        $website->setCode('FR');
        
        $website->setActive(true);
        $website->setDefault(0);
        $website->addLocale($locale);

        $this->getWebsiteMapper()->insert($website);

        $website = new websiteEntity();
        $website->setName('Italy');
        $website->setCode('IT');
        
        $website->setActive(true);
        $website->setDefault(0);
        $website->addLocale($locale);

        $this->getWebsiteMapper()->insert($website);


        $websites = $this->getWebsiteMapper()->findAll();
        $this->assertEquals(count($websites), 2);

        $websites = $this->getWebsiteMapper()->findBy(array('name'=>'Italy'));
        $this->assertEquals(count($websites), 1);
      
    }

    public function testFindById()
    {
        $locale = new localeEntity();
        $locale->setName('French');
        $locale->setLocale('fr_FR');
        $locale->setActiveFront(1);
        $locale->setActiveBack(1);
        $this->getLocaleMapper()->insert($locale);


        $website = new websiteEntity();
        $website->setName('France');
        $website->setCode('FR');
        
        $website->setActive(true);
        $website->setDefault(0);
        $website->addLocale($locale);

        $this->getWebsiteMapper()->insert($website);

        $website = new websiteEntity();
        $website->setName('Italy');
        $website->setCode('IT');
        
        $website->setActive(true);
        $website->setDefault(0);
        $website->addLocale($locale);

        $this->getWebsiteMapper()->insert($website);


        $websites = $this->getWebsiteMapper()->findAll();
        $this->assertEquals(count($websites), 2);
        $website = $websites[0];

        $websites = $this->getWebsiteMapper()->findById($website->getId());
        $this->assertEquals(count($websites), 1); 
    }


    public function testUpdate()
    {
        $locale = new localeEntity();
        $locale->setName('French');
        $locale->setLocale('fr_FR');
        $locale->setActiveFront(1);
        $locale->setActiveBack(1);
        $this->getLocaleMapper()->insert($locale);


        $website = new websiteEntity();
        $website->setName('France');
        $website->setCode('FR');
        
        $website->setActive(true);
        $website->setDefault(0);
        $website->addLocale($locale);

        $this->getWebsiteMapper()->insert($website);

        $website = new websiteEntity();
        $website->setName('Italy');
        $website->setCode('IT');
        
        $website->setActive(true);
        $website->setDefault(0);
        $website->addLocale($locale);

        $this->getWebsiteMapper()->insert($website);


        $websites = $this->getWebsiteMapper()->findAll();
        $this->assertEquals(count($websites), 2);
        $website = $websites[0];

        $website->setName('France2');
        $website = $this->getWebsiteMapper()->update($website);

        $websites = $this->getWebsiteMapper()->findBy(array('name'=>'France2'));
        $this->assertEquals(count($websites), 1);


    }

    public function testRemove()
    {
        $locale = new localeEntity();
        $locale->setName('French');
        $locale->setLocale('fr_FR');
        $locale->setActiveFront(1);
        $locale->setActiveBack(1);
        $this->getLocaleMapper()->insert($locale);


        $website = new websiteEntity();
        $website->setName('France');
        $website->setCode('FR');
        
        $website->setActive(true);
        $website->setDefault(0);
        $website->addLocale($locale);

        $this->getWebsiteMapper()->insert($website);

        $website = new websiteEntity();
        $website->setName('Italy');
        $website->setCode('IT');
        
        $website->setActive(true);
        $website->setDefault(0);
        $website->addLocale($locale);

        $this->getWebsiteMapper()->insert($website);


        $websites = $this->getWebsiteMapper()->findAll();
        $this->assertEquals(count($websites), 2);


        foreach ($websites as $website) {
           $this->getWebsiteMapper()->remove($website); 
        }

        $websites = $this->getWebsiteMapper()->findAll();
        $this->assertEquals(count($websites), 0);
    }
  

       
    public function getWebsiteMapper()
    {

        if (null === $this->websiteMapper) {
            $this->websiteMapper = $this->sm->get('playgroundcore_website_mapper');
        }

        return $this->websiteMapper;
    }

      public function getLocaleMapper()
    {

        if (null === $this->localeMapper) {
            $this->localeMapper = $this->sm->get('playgroundcore_locale_mapper');
        }

        return $this->localeMapper;
    }

    public function tearDown()
    {
        $dbh = $this->em->getConnection();
        unset($this->sm);
        unset($this->em);
        parent::tearDown();
    }


}