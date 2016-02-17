<?php
namespace Poirot\Std\Interfaces;

interface iMagicalFields
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function __set($key, $value);

    /**
     * @param string $key
     * @return mixed
     */
    function __get($key);

    /**
     * @param string $key
     * @return bool
     */
    function __isset($key);

    /**
     * @param string $key
     * @return void
     */
    function __unset($key);
}
