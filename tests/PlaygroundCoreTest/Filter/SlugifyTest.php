<?php

namespace PlaygroundCoreTest\Filter;



class CronTest extends \PHPUnit_Framework_TestCase
{
    protected $traceError = true;
    
    public function setUp()
    {
        parent::setUp();
        $this->slugify  = new \PlaygroundCore\Filter\Slugify; 
    }

    public function testSlugify()
    {

        $string = "vue arriere";
        $return = $this->slugify->filter($string);
        $this->assertEquals("vue-arriere", $return);


        $string = "vue arrière";
        $return = $this->slugify->filter($string);
        $this->assertEquals("vue-arri-re", $return);

    }

}