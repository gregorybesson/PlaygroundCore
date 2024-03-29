<?php

namespace PlaygroundCore\Filter;

/**
 * Martin Hujer's Components
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mhujer@gmail.com so I can send you a copy immediately.
 *
 * @category    Mhujer
 * @package     Mhujer_View_Helper
 * @author      Martin Hujer mhujer@gmail.com
 */

/**
 * @see Zend_Filter_StringToLower
 */
use Laminas\Filter\StringToLower;
/**
 * @see Zend_Filter_StringTrim
 */
use Laminas\Filter\StringTrim;
use PlaygroundCore\Filter\Transliteration;

/**
 * @category   Mhujer
 * @package    Mhujer_Filter
 * @author     Martin Hujer mhujer@gmail.com
 */


class Sanitize implements \Laminas\Filter\FilterInterface
{

    /**
     * Character(s) used to replace delimiters
     *
     * @var string
     */
    protected $_delimiterReplacement;

    /**
     * Word delimiters which are replaced by spaceReplacement string
     *
     * @var array
     */
    protected $_wordDelimiters = array(' ', '.', '\\', '/', '-', '_');


    /**
     * Which characters are not replaced
     *
     * @var array
     */
    protected $_notReplacedChars = array();

    /**
     *
     * @param string $delimiterReplacement
     * @param string|array $wordDelimiters
     */
    public function __construct($delimiterReplacement = '-', $wordDelimiters = null)
    {
        $this->setDelimiterReplacement($delimiterReplacement);
        if (null !== $wordDelimiters) {
            $this->addWordDelimiter($wordDelimiters);
        }
    }

    public function filter($s)
    {
        $filename = pathinfo($s, PATHINFO_FILENAME);
        $extension = pathinfo($s, PATHINFO_EXTENSION);
        return $this->filterSubstring($filename).($extension  ? '.'.$this->filterSubstring($extension) : '');
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns $value filtered to valid url
     *
     * @param  string $value
     * @return string
     */
    public function filterSubstring($s)
    {
        //convert to ascii -> translate strange chars
        $s = $this->convertToAscii($s);
        //trim spaces
        $s = $this->trimString($s);
        //replace delimiters with another character
        $s = $this->replaceDelimiters($s);
        //lower chars
        $s = $this->toLowerChars($s);
        //delete chars except a-z0-9
        $s = $this->trimSpecialsChars($s);
        //replace double dashes with single one
        $s = $this->replaceDoubleDelimiterReplacementWithSingle($s);
        //trim dashes on beginning/end of the string
        $s = $this->trimStartAndEndSpaceReplacement($s);
        return $s;
    }

    public function addNotReplacedChars($notReplaced)
    {
        if (in_array($notReplaced, $this->getNotReplacedChars())) {
            throw new \Exception("Not replaced characterr '$notReplaced' is already there.");
        }
        if (empty($notReplaced)) {
            throw new \Exception('Not replaced character cannot be null.');
        }
        if (is_array($notReplaced)) {
            $this->_notReplacedChars = array_merge($this->getNotReplacedChars(), $notReplaced);
        } else {
            $this->_notReplacedChars[] = $notReplaced;
        }
        return $this;
    }

    /**
     * returns chars which are not replaced
     *
     * @return array
     */
    public function getNotReplacedChars()
    {
        return $this->_notReplacedChars;
    }

    /**
     * Remove not replaced character
     *
     * @param string|array $notReplaced
     * @return Zend_Filter_Sanitize
     */
    public function removeNotReplacedChar($notReplaced)
    {
        if (empty($notReplaced)) {
            throw new \Exception('Not replaced character cannot be null.');
        }
        if (is_array($notReplaced)) {
            foreach ($notReplaced as $n) {
                $this->removeNotReplacedChar($n);
            }
        } else {
            if (! in_array($notReplaced, $this->getNotReplacedChars())) {
                throw new \Exception("Not replaced character '$notReplaced' is not in array.");
            }
            $newArray = array();
            foreach ($this->_notReplacedChars as $n) {
                if ($n != $notReplaced) {
                    $newArray[] = $n;
                }
                $this->_notReplacedChars = $newArray;
            }
        }
        return $this;
    }

    /**
     * Returns the delimiterReplacement option
     *
     * @return string
     */
    public function getDelimiterReplacement()
    {
        return $this->_delimiterReplacement;
    }

    /**
     * Sets the delimiterReplacement option
     *
     * @param  string $delimiterReplacement
     * @return Zend_Filter_Sanitize Provides a fluent interface
     */
    public function setDelimiterReplacement($delimiterReplacement)
    {
        $this->_delimiterReplacement = $delimiterReplacement;
        return $this;
    }

    /**
     * Returns word delimiters array
     *
     * @return array
     */
    public function getWordDelimiters()
    {
        return $this->_wordDelimiters;
    }

    /**
     * Add word delimiter
     *
     * @param string|array $delimiter
     * @return Zend_Filter_Sanitize
     */
    public function addWordDelimiter($delimiter)
    {
        if (in_array($delimiter, $this->getWordDelimiters())) {
            throw new \Exception("Word delimiter '$delimiter' is already there.");
        }
        if (empty($delimiter)) {
            throw new \Exception('Word delimiter cannot be null.');
        }
        if (is_array($delimiter)) {
            $this->_wordDelimiters = array_merge($this->getWordDelimiters(), $delimiter);
        } else {
            $this->_wordDelimiters[] = $delimiter;
        }
        return $this;
    }

    /**
     * Remove word delimiter
     *
     * @param string|array $delimiter
     * @return Zend_Filter_Sanitize
     */
    public function removeWordDelimiter($delimiter)
    {
        if (empty($delimiter)) {
            throw new \Exception('Word delimiter cannot be null.');
        }
        if (is_array($delimiter)) {
            foreach ($delimiter as $delim) {
                $this->removeWordDelimiter($delim);
            }
        } else {
            if (! in_array($delimiter, $this->getWordDelimiters())) {
                throw new \Exception("Word delimiter '$delimiter' is not in delimiters array.");
            }
            $newArray = array();
            foreach ($this->_wordDelimiters as $delim) {
                if ($delim != $delimiter) {
                    $newArray[] = $delim;
                }
                $this->_wordDelimiters = $newArray;
            }
        }
        return $this;
    }

    /**
     * trim spaces, tabs, newlines
     *
     * @param string $s
     * @return string
     */
    private function trimString($s)
    {
        $trimFilter = new \Laminas\Filter\StringTrim();
        return $trimFilter->filter($s);
    }

    /**
     * replace delimiters with another string
     *
     * @param string $s
     * @return string
     */
    private function replaceDelimiters($s)
    {
        foreach ($this->getWordDelimiters() as $delimiter) {
            if ($delimiter == $this->getDelimiterReplacement()) {
                continue;
            }
            if (in_array($delimiter, $this->getNotReplacedChars())) {
                continue;
            }

            $s = str_replace($delimiter, $this->getDelimiterReplacement(), $s);
        }
        return $s;
    }

    /**
     * convert to ascii -> translate strange chars
     *
     * @param string $s
     * @return string
     */
    private function convertToAscii($s)
    {
        $f = new \PlaygroundCore\Filter\Transliteration;
        return $f->filter($s);
    }

    /**
     * to lower chars
     *
     * @param string $s
     * @return string
     */
    private function toLowerChars($s)
    {
        $lowerFilter = new \Laminas\Filter\StringToLower;
        $lowerFilter->setEncoding('utf-8');
        return $lowerFilter->filter($s);
    }

    /**
     * to lower chars
     *
     * @param string $s
     * @return string
     */
    private function trimSpecialsChars($s)
    {
        if (count($this->getNotReplacedChars()) == 0) {
            $reg = '~[^-a-z0-9_]+~';
        } else {
            $reg = '~[^-a-z0-9_' . implode('', $this->getNotReplacedChars()) .']+~';
        }
        return preg_replace($reg, '', $s);
    }

    /**
     * replace double delimiter with single one
     *
     * @param string $s
     * @return string
     */
    private function replaceDoubleDelimiterReplacementWithSingle($s)
    {
        $doubleDelimiterReplacement = $this->getDelimiterReplacement() . $this->getDelimiterReplacement();
        while (strpos($s, $doubleDelimiterReplacement) !== false) {
            $s = str_replace($doubleDelimiterReplacement, $this->getDelimiterReplacement(), $s);
        }
        return $s;
    }

    /**
     * trim dashes on beginning/end of the string
     *
     * @param string $s
     * @return string
     */
    private function trimStartAndEndSpaceReplacement($s)
    {
        return trim($s, $this->getDelimiterReplacement());
    }
}
