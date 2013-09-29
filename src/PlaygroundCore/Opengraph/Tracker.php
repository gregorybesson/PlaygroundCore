<?php
namespace PlaygroundCore\Opengraph;

use PlaygroundCore\Analytics\Ecommerce\Transaction;
use PlaygroundCore\Exception\InvalidArgumentException;

class Tracker
{
    /**
     *
     * @var string
     */
    protected $id;
    
    protected $tags;

    /**
     * Flag if FB opengraph is enabled or not
     *
     * By default tracking is enabled when the tracker is instantiated
     *
     * @var bool
     */
    protected $enableOpengraph = true;

    public function __construct($id)
    {
        $this->setId($id);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function enabled()
    {
        return $this->enableOpengraph;
    }
    
    public function setEnableOpengraph($enableOpengraph = true)
    {
        $this->enableOpengraph = (bool) $enableOpengraph;
    }
    
    public function tags()
    {
        return $this->tags;
    }
    
    public function addTag (Tag $tag)
    {
        if (null === $this->tags) {
            $this->tags = array();
        }
    
        $this->tags[] = $tag;
    }
}
