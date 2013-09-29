<?php
namespace PlaygroundCore\Opengraph;

class Tag
{
    protected $property;
    protected $value;

    public function __construct ($property, $value = null)
    {
        $this->property = $property;
        $this->value   = $value;
    }

    public function getProperty ()
    {
        return $this->property;
    }

    public function setProperty ($property)
    {
        $this->property = $property;
    }

    public function getValue ()
    {
        return $this->value;
    }

    public function setValue ($value)
    {
        $this->value = $value;
    }
}
