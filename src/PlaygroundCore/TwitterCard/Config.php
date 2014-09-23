<?php

namespace PlaygroundCore\TwitterCard;

class Config
{
    /**
     * true will display in the head
     * @var boolean
     */
    protected $enable = false;

    /**
     * default tag values
     * @var array
     */
    protected $tags = array();

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        if (isset($config['enable'])) {
            $this->enable = (bool)$config['enable'];
        }
        
        if (isset($config['default'])) {
            foreach ($config['default'] as $key => $value) {
                $this->tags[] = new Tag($key, $value);
            }
            $this->default = (bool)$config['enable'];
        }
    }
    
    /**
     * @return boolean
     */
    public function enabled()
    {
        return $this->enable;
    }
    
    /**
     * @return array<Tag>
     */
    public function tags()
    {
        return $this->tags;
    }
    
    /**
     * @param \PlaygroundCore\TwitterCard\Tag $tag
     */
    public function addTag(Tag $tag)
    {
        if (null === $this->tags) {
            $this->tags = array();
        }
    
        $this->tags[] = $tag;
    }
    
    /**
     * @param string $key
     * @return string|false
     */
    public function getDefault($key)
    {
        foreach ($this->tags as $tag) {
            if ($tag->getProperty() == $key) {
                return $tag->getValue();
            }
        }
        return false;
    }

}
