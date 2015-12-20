<?php
namespace Poirot\Core\Traits;

trait CloneTrait
{
    function __clone()
    {
        foreach(get_object_vars($this) as $name => $val)
            (!is_object($val)) ?: $this->{$name} = clone $this->{$name};
    }
}
