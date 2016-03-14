<?php
namespace Poirot\Std\Type;

if (class_exists('Poirot\Std\Type\AbstractNSplType'))
    return;

abstract class AbstractNSplType
{
    const __default = null;

    /**
     * Creates a new value of some type
     *
     * @param mixed $initial_value
     * @param bool $strict  If set to true then will throw UnexpectedValueException if value of other type will be assigned. True by default
     * @link http://php.net/manual/en/spltype.construct.php
     */
    abstract function __construct ($initial_value = self::__default, $strict = true);
}
