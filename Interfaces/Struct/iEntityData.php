<?php
namespace Poirot\Std\Interfaces\Struct;

interface iEntityData extends iDataStruct
{
    /**
     * Set Entity
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
     * @return mixed|null NULL value for a property considered __isset false
     */
    function get($key, $default = null);
} 
