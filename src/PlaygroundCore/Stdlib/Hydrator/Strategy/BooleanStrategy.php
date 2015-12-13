<?php
namespace PlaygroundCore\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use DoctrineModule\Stdlib\Hydrator\Strategy\AbstractCollectionStrategy;

class BooleanStrategy implements StrategyInterface
{
    public function extract($value)
    {
        if ($value === null)
        {
            return 0;
        }
 
        if (!is_bool($value))
        {
            throw new \RuntimeException('$value is expected to be boolean.');
        }
 
        return $value === true ? 1 : 0;
    }
 
    public function hydrate($value)
    {
        if ($value === null || $value === '')
        {
            return 0;
        }
 
        if (!in_array($value, [0, 1]))
        {
            throw new \RuntimeException('$value is expected to be 0 or 1.');
        }
 
        return $value == 1 ? true : false;
    }
}