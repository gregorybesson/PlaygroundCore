<?php

namespace PlaygroundCore\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;


/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="locale")
 */
class Locale implements InputFilterAwareInterface
{

    protected $inputFilter;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * name
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * locale
     * @ORM\Column(type="string", length=5, nullable=false)
     */
    protected $locale;

     /**
     * active_back
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $active_back = 0;
     /**
     * active_front
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $active_front = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @param int $id
     * @return Locale
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /** @PrePersist */
    public function createChrono()
    {
        $this->created_at = new \DateTime("now");
        $this->updated_at = new \DateTime("now");
    }

    /** @PreUpdate */
    public function updateChrono()
    {
        $this->updated_at = new \DateTime("now");
    }

    /**
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Locale
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * @return string $locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return Locale
     */
    public function setLocale($locale)
    {
        $this->locale = (string) $locale;

        return $this;
    }


    /**
     * @param int $id
     * @return Locale
     */
    public function setActiveFront($activeFront)
    {
        $this->active_front = (int) $activeFront;

        return $this;
    }

    /**
     * @return int $id
     */
    public function getActiveFront()
    {
        return $this->active_front;
    }

    /**
     * @param int $activeBack
     * @return Locale
     */
    public function setActiveBack($activeBack)
    {
        $this->active_back = (int) $activeBack;

        return $this;
    }

    /**
     * @return int $active_back
     */
    public function getActiveBack()
    {
        return $this->active_back;
    }

    /**
     * @return string created_at
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return string updated_at
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param string $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getFlag()
    {
        $flags = explode('_', $this->getLocale());

        if(empty($flags[1])) {
            return '';
        }

        return "/lib/images/flag/".strtolower($flags[1]);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        if (isset($data['name']) && $data['name'] != null) {
            $this->name = $data['name'];
        }
        if (isset($data['locale']) && $data['locale'] != null) {
            $this->locale = $data['locale'];
        }
    }



    /**
    * setInputFilter
    * @param InputFilterInterface $inputFilter
    */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
    * getInputFilter
    *
    * @return  InputFilter $inputFilter
    */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $this->inputFilter = $inputFilter;
            $factory = new InputFactory();
        }
        return $this->inputFilter;
    }
}
