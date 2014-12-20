<?php
namespace Poirot\Core\Entity;

use Poirot\Core\Entity;

class IteratorConveyor implements \Iterator, \Countable
{
    /**
     * @var Entity
     */
    protected $origin;

    /**
     * @var array Entity Props
     */
    protected $keys = [];

    /**
     * @var string current key
     */
    protected $current;

    /**
     * Construct
     *
     * @param Entity $entity
     */
    function __construct(Entity $entity)
    {
        $this->origin = $entity;

        $this->keys = $entity->keys();
    }

    /**
     * Get Origin Entity
     *
     * @return Entity
     */
    function getEntity()
    {
        return $this->origin;
    }

    // Implement Iterator:

    public function current()
    {
        return $this->origin->get($this->current);
    }

    public function next()
    {
        $this->current = next($this->keys);
    }

    public function key()
    {
        return $this->current;
    }

    public function valid()
    {
        return ($this->current !== false);
    }

    public function rewind()
    {
        return $this->current = reset($this->keys);
    }

    // Implement Countable:

    public function count()
    {
        return count($this->origin->keys());
    }
}
