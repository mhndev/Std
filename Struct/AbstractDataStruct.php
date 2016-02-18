<?php
namespace Poirot\Std\Struct;

use Poirot\Std\Interfaces\Struct\iDataStruct;
use Traversable;

abstract class AbstractDataStruct implements iDataStruct
{
    /**
     * AbstractStruct constructor.
     *
     * @param null|array|\Traversable $data
     */
    function __construct($data = null)
    {
        if ($data !== null)
            $this->from($data);
    }

    /**
     * Set Struct Data From Array
     *
     * @param array|\Traversable $data
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    final function from($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable)
            throw new \InvalidArgumentException(sprintf(
                'Data must be instance of \Traversable or array. given: (%s)'
                , \Poirot\Std\flatten($data)
            ));

        $this->doSetFrom($data);

        return $this;
    }

    /**
     * Empty from all values
     * @return $this
     */
    function emptyy()
    {
        foreach($this as $k => $v)
            $this->__unset($k);

        return $this;
    }

    /**
     * Is Empty?
     * @return bool
     */
    function isEmpty()
    {
        $isEmpty = true;
        foreach($this as $v) {
            $isEmpty = false;
            break;
        }

        return $isEmpty;
    }

    /**
     * Proxy to __isset
     * @param mixed $key
     * @return bool
     */
    function has($key)
    {
        return $this->__isset($key);
    }

    /**
     * Proxy to __unset
     * @param mixed $key
     * @return $this
     */
    function del($key)
    {
        $this->__unset($key);
        return $this;
    }

    /**
     * Do Set Data From
     * @param array|\Traversable $data
     */
    abstract protected function doSetFrom($data);

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    abstract function getIterator();
}