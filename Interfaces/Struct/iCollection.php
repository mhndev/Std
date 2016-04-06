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
     * Remove All Entities Item
     *
     * @return $this
     */
    function clean();
}
