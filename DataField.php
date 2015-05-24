<?php
namespace Poirot\Core;

use Poirot\Core\Interfaces\iDataField;

class DataField implements iDataField
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function __set($key, $value)
    {
        $this->{$key} = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    function __get($key)
    {
        if (!$this->__isset($key))
            return null;

        return $this->{$key};
    }

    /**
     * @param string $key
     * @return bool
     */
    function __isset($key)
    {
        return isset($this->{$key});
    }

    /**
     * @param string $key
     * @return void
     */
    function __unset($key)
    {
        if ($this->__isset($key))
            unset($this->{$key});
    }
}
