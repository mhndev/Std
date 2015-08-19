<?php
namespace Poirot\Core\Interfaces;

interface EntityInterface extends iDataSetConveyor
{
    /**
     * Set Entity From A Given Resource
     *
     * - you have to set resource internally that can given
     *   by getResource method later
     *
     * @param mixed $resource
     *
     * @return $this
     */
    function from($resource);

    /**
     * Get Resource Data
     *
     * - if no resource data was set on from(method)
     *   return $this
     *
     * @return mixed|$this
     */
    function getResource();

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
