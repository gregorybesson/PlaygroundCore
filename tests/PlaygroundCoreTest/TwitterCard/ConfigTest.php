<?php

namespace PlaygroundCoreTest\TwitterCard;



use PlaygroundCore\TwitterCard\Tag;
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    
    protected $config;

    public function testConfigReturnTagsCorrectly()
    {
        $tagsDefault = array(
            'twitter:site'          => 'Site Playground',
            'twitter:card'          => 'summary_large_image',
            'twitter:title'         => 'Title Playground',
            'twitter:description'   => 'Description Playground',
            'twitter:image:src'     => ''
        );
        
        $this->config  = new \PlaygroundCore\TwitterCard\Config(array(
            'enable'     => true,
            'useDefault' => true,
            'default'    => $tagsDefault
        ));
        
        $tags = array();
        
        $this->config->addTag(new Tag('twitter:site', 'Site Custom'));
        $tagsDefault['twitter:site'] = 'Site Custom';
        $tags['twitter:site'] = 'Site Custom';
        
        $this->config->addTag(new Tag('twitter:somme:property', 'Somme Value'));
        $tagsDefault['twitter:somme:property'] = 'Somme Value';
        $tags['twitter:somme:property'] = 'Somme Value';

        // tags + defaults
        $this->assertEquals($tagsDefault, $this->config->getTags(true));
        
        // only setted tags (defaults not included)
        $this->assertEquals($tags, $this->config->getTags(false));
        
        // tags to return determined by config
        $this->assertEquals($tagsDefault, $this->config->getTags());
    }
    
    public function testConfigCanRunWithoutConfig()
    {
        $this->config  = new \PlaygroundCore\TwitterCard\Config(null);
    
        $tags = array();
        $tagsDefault = array();
        
        // tags + defaults
        $this->assertEquals($tagsDefault, $this->config->getTags(true));
        
        // only setted tags (defaults not included)
        $this->assertEquals($tags, $this->config->getTags(false));
        
        // tags to return determined by config
        $this->assertEquals($tagsDefault, $this->config->getTags());
    
        $this->config->addTag(new Tag('twitter:site', 'Site Custom'));
        $tagsDefault['twitter:site'] = 'Site Custom';
        $tags['twitter:site'] = 'Site Custom';
    
        $this->config->addTag(new Tag('twitter:somme:property', 'Somme Value'));
        $tagsDefault['twitter:somme:property'] = 'Somme Value';
        $tags['twitter:somme:property'] = 'Somme Value';
    
        // tags + defaults
        $this->assertEquals($tagsDefault, $this->config->getTags(true));
    
        // only setted tags (defaults not included)
        $this->assertEquals($tags, $this->config->getTags(false));
    
        // tags to return determined by config
        $this->assertEquals($tagsDefault, $this->config->getTags());
    }

}
