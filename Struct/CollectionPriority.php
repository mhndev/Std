<?php
namespace Poirot\Std\Struct;

use Poirot\Std\Interfaces\Struct\iCollection;

class CollectionPriority extends \SplPriorityQueue
    implements iCollection
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
    function insert($data, $priority = 0)
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
     * Does the queue contain the given datum?
     *
     * - if datum exists return index otherwise false
     *
     * @param  mixed $data
     *
     * @return false|int Index of item include 0
     */
    function has($data)
    {
        foreach ($this->__mapped_items as $index => $item) {
            if ($item['data'] === $data)
                return $index;
        }

        return false;
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
     * @return $this
     */
    function del($data)
    {
        $index = $this->has($data);

        if ($index === false)
            return false;

        $current = $this->__mapped_items;
        unset($current[$index]);
        $this->clean();
        foreach ($current as $item)
            $this->insert($item['data'], $item['priority']);

        return $this;
    }

    /**
     * Remove All Entities Item
     *
     * @return $this
     */
    function clean()
    {
        foreach($this as $i)
            // Just Iterate Over Entities will delete items
            VOID;

        $this->__mapped_items = [];
        return $this;
    }
}
