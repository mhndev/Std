<?php
namespace Poirot\Std\Interfaces\Struct;

interface iCollection extends /*\Traversable,*/ \Countable
{
    /**
     * Insert Data Into Collection
     *
     * @param mixed $datum
     *
     * @return $this
     */
    function insert($datum);

    /**
     * Does collection contain the given datum?
     *
     * @param  mixed $datum
     *
     * @return true|false
     */
    function has($datum);

    /**
     * Remove an item from the collection
     *
     * @param mixed $data
     *
     * @return $this
     */
    function del($data);

    /**
     * // TODO 7 support keywords as method ame
     * Remove All Entities Item
     *
     * @return $this
     */
    function emptyy();
}
