<?php
namespace Poirot\Core\Traits;

trait CloneTrait
{
    function __clone()
    {
        $_f__clone_array = function($arr) use (&$_f__clone_array) {
            foreach ($arr as &$v) {
                if (is_array($v))
                    $_f__clone_array($v);
                elseif (is_object($v))
                    $v = clone $v;
            }
        };

        foreach($this as &$val) {
            if (is_array($val))
                $_f__clone_array($val);
            elseif (is_object($val))
                $val = clone $val;
        }
    }
}
