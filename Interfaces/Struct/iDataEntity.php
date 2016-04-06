<?php
namespace Poirot\Std\Interfaces\Struct;

interface iDataEntity extends iData
{
    /**
     * Set Entity
     *
     * - values that set to null must be unset from entity
     *
     * @param mixed      $key   Entity Key
     * @param mixed|null $value Entity Value
     *                          NULL value for a property considered __isset false
     *
     * @return $this
     */
    function set($key, $value);

    /**
     * Get Entity Value
     *
     * @param mixed $key     Entity Key
     * @param null  $default Default If Not Value/Key Exists
     *
     * @throws \Exception Value not found
     * @return mixed|null NULL value for a property considered __isset false
     */
    function get($key, $default = null);
} 
