<?php
namespace Poirot\Std\Struct;

use Poirot\Std\Interfaces\Struct\iDataStruct;
use Poirot\Std\Struct\Traits\AbstractDataStructTrait;

abstract class AbstractDataStruct implements iDataStruct
{
    use AbstractDataStructTrait;

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
     * Do Set Data From
     * @param array|\Traversable $data
     */
    abstract protected function doSetFrom($data);

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    abstract function getIterator();
}
