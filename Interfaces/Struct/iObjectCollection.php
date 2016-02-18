<?php
namespace Poirot\Std\Interfaces\Struct;

interface iObjectCollectionStruct extends iCollectionStruct
{
    /**
     * Attach Object
     *
     * - replace object with new data if exists
     *
     * note: recommend that object index by Unified ETag
     *       for better search and performance
     *
     * @param object     $object
     * @param array      $data   it can be used to attach some data
     *                           this data can be available for some codes
     *                           block that need this data ...
     *                           in case of render, view renderer can match
     *                           headers that attached by itself and make
     *                           some condition.
     *
     * @throws \InvalidArgumentException Object Type Mismatch
     * @return string ETag Hash Identifier of object
     */
    function insert($object, array $data = []);

    /**
     * Checks if the storage contains a specific object
     *
     * @param string|object $hashOrObject
     *
     * @return boolean
     */
    function has($hashOrObject);

    /**
     * Detach By ETag Hash Or Object Match
     *
     * @param string|object $hashOrObject
     *
     * @return boolean Return true on detach match otherwise false
     */
    function del($hashOrObject);

    // ...

    /**
     * Search for first object that match accurate data
     *
     * @param array $data
     *
     * @return \Traversable|\Generator
     */
    function find(array $data);

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
    function getData($object);

    /**
     * Set Data For Stored Object
     *
     * note: use ETag to attain target object for
     *       performance
     *
     * @param object $object
     * @param array  $data
     *
     * @throws \Exception Object not stored
     * @return $this
     */
    function setData($object, array $data);

    /**
     * Calculate a unique identifier for the contained objects
     *
     * @param object $object
     *
     * @return string
     */
    function genETag($object);
}
