<?php
namespace Poirot\Std\Struct;

use Poirot\Std\Interfaces\Struct\iObjectCollection;

/*
$c = new ObjectCollection();

$data = [
    'firstName' => 'Payam',
    'lastName' => 'Naderi',
    'birthDate' => '1983-08-13',
];
$c->insert(new DataMean($data), /* info tags for this data * / $data);

$data = [
    'firstName' => 'Payam',
    'lastName'  => 'Ezzati',
    'birthDate' => '1983-08-13',
];
$c->insert(new DataMean($data), $data);
print PHP_EOL. sprintf('You Add %s item(s) into collection.', count($c));
foreach ($c->find(['firstName' => 'Payam',]) as $person)
    print PHP_EOL.( $person->firstName );

print PHP_EOL;
die('>_');
*/

class ObjectCollection implements iObjectCollection, \Iterator
{
    protected $_objs  = [
        /*
        '0xc33223Etag' => [
            'data' => [
                'etag'   => '0xc33223Etag',
                'extra'  => 'This is extra added data',
            ],
            'object'     => StoredObject,
        ],
        // ...
        */
    ];

    protected $__cached_obj_tags = [];

    /** @var string current iterator key */
    protected $_trav__curr_index;

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
    function insert($object, array $data = [])
    {
        $this->doValidateObject($object);

        if (!empty($data) && $data == array_values($data))
            throw new \InvalidArgumentException('Data tags must be associative array.');

        $data['etag'] = $this->genETag($object); // so we can search by etag hash

        $hash = $this->genETag($object);
        if (isset($this->_objs[$hash]))
            ## merge data if object exists
            $data = array_merge($this->_objs[$hash]['data'], $data);

        $this->_objs[$hash] = ['object' => $object, 'data' => $data];

        return $hash;
    }

    /**
     * @param $object
     *
     * @throws \InvalidArgumentException
     */
    protected function doValidateObject($object)
    {
        if ($object === null || (empty($object) && $object !==0 && $object !=='0') )
            throw new \InvalidArgumentException(sprintf(
                'Object can`t be empty, given: (%s).'
                , \Poirot\Std\flatten($object)
            ));
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
            $hash = $this->genETag($hashOrObject);

        return (array_key_exists($hash, $this->_objs));
    }

    /**
     * Detach By ETag Hash Or Object Match
     *
     * @param string|object $hashOrObject
     *
     * @return boolean Return true on detach match otherwise false
     */
    function del($hashOrObject)
    {
        $hash = $hashOrObject;
        if (is_object($hashOrObject))
            $hash = $this->genETag($hashOrObject);

        if (!array_key_exists($hash, $this->_objs))
            return false;

        unset($this->_objs[$hash]);

        return true;
    }

    /**
     * // TODO 7 support keywords as method ame
     * Remove All Entities Item
     *
     * @return $this
     */
    function emptyy()
    {
        foreach($this as $key => $v)
            $this->del($key);

        return $this;
    }

    /**
     * Search for first object that match accurate data
     *
     * - with data[':etag' => 'xxx'] you can search for
     *   specific object by hash tag
     *
     * @param array $data
     *
     * @return \Traversable|\Generator
     */
    function find(array $data)
    {
        if ($data == array_values($data))
            throw new \InvalidArgumentException('Data tags must be associative array.');

        // .....................
        if (isset($data[':etag']) && $hash = $data[':etag'])
            // ETags is unique and if present only search for etag match
            if ($this->has($hash)) {
                yield $hash => $this->_objs[$hash]['object'];
                return;
            }
        // ............................................................

        foreach($this->_objs as $hash => $obAr) {
            $obData = $obAr['data'];
            if ($data == array_intersect_assoc($obData, $data))
                yield $hash => $this->_objs[$hash]['object'];
        }

        return;
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

        $hash = $this->genETag($object);

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

        $hash = $this->genETag($object);

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
    function genETag($object)
    {
        $this->doValidateObject($object);

        $hash = md5(\Poirot\Std\flatten($object));

        return $hash;
    }


    // Implement Iterator:

    /**
     * @inheritdoc
     */
    public function current()
    {
        $current = $this->_trav__curr_index;

        return $this->_objs[$current]['object'];
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $data = next($this->_objs);

        $this->_trav__curr_index = ($data) ? $data['data']['etag'] : false;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->_trav__curr_index;
    }

    /**
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->_trav__curr_index;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $data = reset($this->_objs);
        $this->_trav__curr_index = $data['data']['etag'];
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
