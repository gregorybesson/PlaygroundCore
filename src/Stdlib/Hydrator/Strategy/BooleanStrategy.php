<?php
namespace PlaygroundCore\Stdlib\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

class BooleanStrategy implements StrategyInterface
{
    public function extract($value, ?object $object = null)
    {
        if ($value === null) {
            return 0;
        }

        if (in_array($value, [0, 1])) {
            return $value;
        }


        if (!is_bool($value)) {
            throw new \RuntimeException('$value is expected to be boolean.');
        }

        return $value === true ? 1 : 0;
    }

    public function hydrate($value, ?array $data)
    {
        if ($value === null || $value === '') {
            return false;
        }

        if ($value === true || $value === false) {
            return $value;
        }

        if (!in_array($value, [0, 1])) {
            throw new \RuntimeException('$value is expected to be 0 or 1.');
        }

        return $value == 1 ? true : false;
    }
}
