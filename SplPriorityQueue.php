<?php
namespace Poirot\Std;

class SplPriorityQueue extends \SplPriorityQueue
{
    protected $__sdec = PHP_INT_MAX;

    /**
     * @var array a map to real existence data on spl queue for performance
     */
    protected $__mapped_items = [];

    /**
     * Insert a value with a given priority
     *
     * @see https://mwop.net/blog/253-Taming-SplPriorityQueue.html
     *
     * @param  mixed $data
     * @param  mixed $priority
     *
     * @return void
     */
    function insert($data, $priority)
    {
        if (!is_array($priority))
            $priority = [$priority, $this->__sdec--];

        $this->__mapped_items[] = [
            'priority' => $priority,
            'data'     => $data
        ];

        parent::insert($data, $priority);
    }

    /**
     * Remove an item from the queue
     *
     * Note: this removes the first item matching the provided item found. If
     * the same item has been added multiple times, it will not remove other
     * instances.
     *
     * @param mixed $data
     *
     * @return bool False if the item was not found, true otherwise.
     */
    function remove($data)
    {
        $found = false;
        foreach ($this->__mapped_items as $index => $item)
            if ($item['data'] === $data) {
                $found = true;
                break;
            }

        if ($found) {
            unset($this->__mapped_items[$index]);

            $this->removeAll();

            foreach ($this->__mapped_items as $item)
                $this->insert($item['data'], $item['priority']);
        }

        return $found;
    }

    /**
     * Remove All Entities Item
     *
     * @return $this
     */
    function removeAll()
    {
        foreach($this as $i) {
            // Just Iterate Over Entities will delete items
        }

        $this->__mapped_items = [];

        return $this;
    }

    /**
     * Does the queue contain the given datum?
     *
     * @param  mixed $data
     *
     * @return mixed|false
     */
    function find($data)
    {
        foreach ($this->__mapped_items as $item) {
            if ($item['data'] === $data)
                return $data;
        }

        return false;
    }

    /**
     * To array
     *
     * note: array will be priority => data pairs
     *
     * @return array
     */
    function toArray()
    {
        $array = array();
        foreach (clone $this as $item) {
            $array[] = $item;
        }
        return $array;
    }
}
