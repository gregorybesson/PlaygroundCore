<?php
namespace PlaygroundCore\Filter;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class Slugify extends \Zend\Filter\AbstractUnicode
{
    /**
     * @var array
     */
    protected $options = array(
        'encoding' => null,
    );

    /**
     * Constructor
     *
     * @param string|array|Traversable $encodingOrOptions OPTIONAL
     */
    public function __construct($encodingOrOptions = null)
    {
        if ($encodingOrOptions !== null) {
            if (!static::isOptions($encodingOrOptions)) {
                $this->setEncoding($encodingOrOptions);
            } else {
                $this->setOptions($encodingOrOptions);
            }
        }
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns the string $value, converting characters to lowercase as necessary
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        setlocale(LC_CTYPE, 'en_US.UTF-8');
        $value = iconv("UTF-8","ASCII//TRANSLIT//IGNORE",$value);
        $value = strtolower($value);
        $value = str_replace("'", '', $value);
        $value = preg_replace('([^a-zA-Z0-9_-]+)', '-', $value);
        $value = preg_replace('(-{2,})', '-', $value);
        $value = trim($value, '-');
        return $value;
    }
}
