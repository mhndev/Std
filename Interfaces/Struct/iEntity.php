<?php
namespace Poirot\Std\Interfaces\Struct;

interface iEntity extends iStruct
{
    /**
     * Set Entity
     *
     * @param string $key   Entity Key
     * @param mixed  $value Entity Value
     *
     * @return $this
     */
    function set($key, $value);

    /**
     * Get Entity Value
     *
     * @param string $key     Entity Key
     * @param null   $default Default If Not Value/Key Exists
     *
     * @return mixed
     */
    function get($key, $default = null);

    /**
     * Has Entity With key?
     *
     * @param string $key Entity Key
     *
     * @return boolean
     */
    function has($key);

    /**
     * Is Entity Empty?
     *
     * @return boolean
     */
    function isEmpty();

    /**
     * Empty Entity Data
     *
     * @return $this
     */
    function clean();

    /**
     * Delete Entity With Key
     *
     * @param string $key Entity Key
     *
     * @return $this
     */
    function del($key);

    /**
     * Get Entity Props. Keys
     *
     * @return array
     */
    function keys();
} 
