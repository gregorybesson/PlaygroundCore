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
     * true will take the default tag if not found in $tags
     * @var boolean
     */
    protected $useDefault = true;

    /**
     * tag values
     * @var array<Tag>
     */
    protected $tags = array();
    
    /**
     * default tag values
     * @var array<Tag>
     */
    protected $defaultTags = array();

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        if (isset($config['enable'])) {
            $this->enable = (bool)$config['enable'];
        }
        if (isset($config['useDefault'])) {
            $this->useDefault = (bool)$config['useDefault'];
        }
        if (isset($config['default'])) {
            foreach ($config['default'] as $key => $value) {
                $this->defaultTags[] = new Tag($key, $value);
            }
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
     * @param boolean $enable
     * @return \PlaygroundCore\TwitterCard\Config
     */
    public function setEnabled($enable)
    {
        $this->enable = $enable;
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function useDefault()
    {
        return $this->enable;
    }
    
    /**
     * @param boolean $useDefault
     * @return \PlaygroundCore\TwitterCard\Config
     */
    public function setUseDefault($useDefault)
    {
        $this->useDefault = $useDefault;
        return $this;
    }
    
    /**
     * return the tags merged with the defaults
     * @return array
     */
    public function getTags($useDefault = null)
    {
        if (is_null($useDefault)) {
            $useDefault = $this->useDefault;
        }
        $tags = array();
        foreach ($this->tags as $tag) {
            if (!array_key_exists($tag->getProperty(), $tags)) {
                $tags[$tag->getProperty()] = $tag->getValue();
            }
        }
        if ($useDefault) {
            foreach ($this->defaultTags as $tag) {
                if (!array_key_exists($tag->getProperty(), $tags)) {
                    $tags[$tag->getProperty()] = $tag->getValue();
                }
            }
        }
        return $tags;
    }
    
    /**
     * @return array<Tag>
     */
    public function defaultTags()
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
    
    /**
     * @param string $key
     * @param boolean $canUseDefault
     * @return string|false
     */
    public function getTag($key, $canUseDefault = true)
    {
        foreach ($this->tags as $tag) {
            if ($tag->getProperty() == $key) {
                return $tag->getValue();
            }
        }
        if ($canUseDefault) {
            foreach ($this->defaultTags as $tag) {
                if ($tag->getProperty() == $key) {
                    return $tag->getValue();
                }
            }
        }
        return false;
    }
}
