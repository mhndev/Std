<?php
namespace Poirot\Std\Interfaces\Struct;

/**
 * Objects that implement this interface can interchange data
 * provided with each others
 *
 * - NULL value MUST considered as undefined, empty, not set
 *
 * ! iterator_to_array
 */
interface iDataStruct extends \IteratorAggregate, \Countable
{
    /**
     * Set Struct Data From Array
     *
     * @param array|\Traversable|null $data
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function from($data);

    /**
     * Empty from all values
     * @return $this
     */
    function clean();

    /**
     * Is Empty?
     * @return bool
     */
    function isEmpty();

    /**
     * NULL value for a property considered __isset false
     * @param mixed $key
     * @return bool
     */
    function has($key);

    /**
     * NULL value for a property considered __isset false
     * @param mixed $key
     * @return $this
     */
    function del($key);
}

