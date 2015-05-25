<?php
namespace Poirot\Core;

use Poirot\Core\Interfaces\iObjectCollection;

class ObjectCollection implements iObjectCollection
{
    protected $_objs  = [];

    protected $__cached_obj_tags = [];

    /**
     * @var string current iterator key
     */
    protected $_itr_current;

    /**
     * Attach Object
     *
     * - replace object with new data if exists
     *
     * note: recommend that object index by Unified ETag
     *       for better search and performance
     *
     * @param object $object
     * @param array $data    associate array that it can be used to attach some data
     *                       this data can be available for some codes
     *                       block that need this data ...
     *                       in case of render, view renderer can match
     *                       headers that attached by itself and make
     *                       some condition.
     *
     * @throws \InvalidArgumentException Object Type Mismatch
     * @return string ETag Hash Identifier of object
     */
    function attach($object, array $data = [])
    {
        $this->_validateObject($object);

        if ($data == array_values($data))
            throw new \InvalidArgumentException('Data tags must be associative array.');

        $data['etag'] = $this->getETag($object); // so we can search by etag hash

        $hash = $this->getETag($object);
        $this->_objs[$hash] = ['object' => $object, 'data' => $data];

        return $hash;
    }

    /**
     * @param $object
     *
     * @throws \InvalidArgumentException
     */
    protected function _validateObject($object)
    {
        if (!is_object($object))
            throw new \InvalidArgumentException(sprintf(
                'Object must be an object interface, "%s" given.'
                , is_object($object) ? get_class($object) : gettype($object)
            ));
    }

    /**
     * Detach By ETag Hash Or Object Match
     *
     * @param string|object $hashOrObject
     *
     * @return boolean Return true on detach match otherwise false
     */
    function detach($hashOrObject)
    {
        $hash = $hashOrObject;
        if (is_object($hashOrObject))
            $hash = $this->getETag($hashOrObject);

        if (!array_key_exists($hash, $this->_objs))
            return false;

        unset($this->_objs[$hash]);

        return true;
    }

    /**
     * Checks if the storage contains a specific object
     *
     * @param string|object $hashOrObject
     *
     * @return boolean
     */
    function has($hashOrObject)
    {
        $hash = $hashOrObject;
        if (is_object($hashOrObject))
            $hash = $this->getETag($hashOrObject);

        return (array_key_exists($hash, $this->_objs));
    }

    /**
     * Search for first object that match accurate data
     *
     * // TODO search case-insensitive
     *
     * @param array $data
     *
     * @return array[object]
     */
    function search(array $data)
    {
        if ($data == array_values($data))
            throw new \InvalidArgumentException('Data tags must be associative array.');

        $return = [];

        // .....................
        if (isset($data['etag']) && $hash = $data['etag'])
            // ETags is unique and if present only search for etag match
            if ($this->has($hash))
                return [ $hash => $this->_objs[$hash]['object'] ];
        // ............................................................

        foreach($this->_objs as $hash => $obAr) {
            $obData = $obAr['data'];
            if ($data == array_intersect($obData, $data))
                $return[$hash] = $this->_objs[$hash]['object'];
        }

        return $return;
    }

    /**
     * Get Tag Data Of Specific Object
     *
     * note: use ETag to attain target object for
     *       performance
     *
     * @param $object
     *
     * @throws \Exception Object not stored
     * @return array
     */
    function getData($object)
    {
        if (!$this->has($object))
            throw new \Exception('Object Not Found.');

        $hash = $this->getETag($object);

        return $this->_objs[$hash]['data'];
    }

    /**
     * Set Data For Stored Object
     *
     * note: use ETag to attain target object for
     *       performance
     *
     * @param object $object
     * @param array $data Associative Array
     *
     * @throws \Exception Object not stored
     * @return $this
     */
    function setData($object, array $data)
    {
        if (!$this->has($object))
            throw new \Exception('Object Not Found.');

        if ($data == array_values($data))
            throw new \InvalidArgumentException('Data tags must be associative array.');

        $hash = $this->getETag($object);

        $this->_objs[$hash]['data'] = $data;

        return $this;
    }

    /**
     * Calculate a unique identifier for the contained objects
     *
     * @param object $object
     *
     * @return string
     */
    function getETag($object)
    {
        $this->_validateObject($object);

        $hash = md5(serialize($object));

        return $hash;
    }


    // Implement Iterator:

    /**
     * @inheritdoc
     */
    public function current()
    {
        $current = $this->_itr_current;

        return $this->_objs[$current]['object'];
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $data = next($this->_objs);

        $this->_itr_current = ($data) ? $data['data']['etag'] : $data;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        $this->_itr_current;
    }

    /**
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return ($this->_itr_current !== false);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $data = reset($this->_objs);
        $this->_itr_current = $data['data']['etag'];
    }


    // Implement Countable:

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->_objs);
    }

}
