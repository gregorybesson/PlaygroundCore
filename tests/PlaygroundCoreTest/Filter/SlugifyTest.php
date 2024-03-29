<?php

namespace PlaygroundCoreTest\Filter;

class CronTest extends \PHPUnit\Framework\TestCase
{
    protected $traceError = true;
    
    protected function setUp(): void
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
        $this->assertEquals("vue-arriere", $return);


        $string = "groß"; // grand
        $return = $this->slugify->filter($string);
        $this->assertEquals("gross", $return);

        $string = "Προσεγμένο ταμπλό"; // greek
        $return = $this->slugify->filter($string);
        $this->assertEquals("prosegmeno-tamplo", $return);

    }
}
