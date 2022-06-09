<?php
namespace PlaygroundCore\Stdlib\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use Doctrine\Laminas\Hydrator\Strategy\AbstractCollectionStrategy;
use Doctrine\Laminas\Hydrator\Strategy\CollectionStrategyInterface;

// This class fill a gap in Doctrine Hydrator : When the attribute is an object, we have to call getId()
class ObjectStrategy extends AbstractCollectionStrategy implements CollectionStrategyInterface
{
    public function extract($value, ?object $object = null)
    {
        if (is_numeric($value) || $value === null) {
            return $value;
        }

        return $value->getId();
    }

    public function hydrate($value, ?array $data)
    {
        return $value;
    }
}
