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
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="formgen")
 */
class Formgen implements InputFilterAwareInterface
{

    protected $inputFilter;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * title
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * description
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $description;

    /**
     * formjsonified
     * @ORM\Column(type="text", nullable=false)
     */
    protected $formjsonified;

    /**
     * formtemplate
     * @ORM\Column(type="text", nullable=false)
     */
    protected $formtemplate;

     /**
     * @ORM\ManyToOne(targetEntity="PlaygroundCore\Entity\Locale", inversedBy="locale")
     */
    protected $locale;

     /**
     * active
     * @ORM\Column(type="boolean")
     */
    protected $active = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @param string $id
     * @return Locale
     */
    public function setId($id)
    {
        $this->id = (string) $id;

        return $this;
    }

    /**
     * @return string $id
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
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Formgen
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;

        return $this;
    }

    /**
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Formgen
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;

        return $this;
    }

    /**
     * @return string $formjsonified
     */
    public function getFormjsonified()
    {
        return $this->formjsonified;
    }

    /**
     * @param string $formjsonified
     * @return Formgen
     */
    public function setFormjsonified($formjsonified)
    {
        $this->formjsonified = $formjsonified;

        return $this;
    }

    /**
     * @return string $formtemplate
     */
    public function getFormTemplate()
    {
        return $this->formtemplate;
    }

    /**
     * @param string $formtemplate
     * @return Formgen
     */
    public function setFormTemplate($formtemplate)
    {
        $this->formtemplate = $formtemplate;

        return $this;
    }

    /**
     * @return string $active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param string $active
     * @return Formgen
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return datetime $created_at
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param datetime $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return datetime $updated_at
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param datetime $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return datetime $updated_at
     */
    public function getLocale()
    {
        return $this->locale;
    }
    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        if (isset($data['title']) && $data['title'] != null) {
            $this->title = $data['title'];
        }
        if (isset($data['description']) && $data['description'] != null) {
            $this->description = $data['description'];
        }
        if (isset($data['formjsonified']) && $data['formjsonified'] != null) {
            $this->formjsonified = $data['formjsonified'];
        }
        if (isset($data['formtemplate']) && $data['formtemplate'] != null) {
            $this->formtemplate = $data['formtemplate'];
        }
        if (isset($data['active']) && $data['active'] != null) {
            $this->active = $data['active'];
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
