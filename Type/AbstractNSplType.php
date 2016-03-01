<?php
namespace Poirot\Std\Type;

abstract class AbstractNSplType
{
    const __default = null;

    abstract function __construct ($initial_value = self::__default, $strict = true);
}
