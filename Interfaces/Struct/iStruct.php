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
interface iStruct extends \IteratorAggregate
{
    /**
     * Set Struct Data From Array
     *
     * @param array|\Traversable $data
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function from($data);

    /**
     * // TODO 7 support keywords as method ame
     * Empty from all values
     * @return $this
     */
    function emptyy();

    /**
     * Is Empty?
     * @return bool
     */
    function isEmpty();

    // ...

    /**
     * NULL value for a property considered __isset false
     * @param string $key
     * @return bool
     */
    function __isset($key);

    /**
     * NULL value for a property considered __isset false
     * @param string $key
     * @return void
     */
    function __unset($key);
}
