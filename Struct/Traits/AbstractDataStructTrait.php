<?php
namespace Poirot\Std\Struct\Traits;

/*
With PHP is not possible to define implementation Interface-
for Traits so in classes that uses DataStruct as trait it must
extend implements.

class ent extends Spinal
    implements \IteratorAggregate
{
    use EntityDataTrait;

    function __construct($input)
    {
        $this->from($input);
    }
}
*/

trait AbstractDataStructTrait
{
    /**
     * Set Struct Data From Array
     *
     * @param array|\Traversable|null $data
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function from($data)
    {
        if ($data === null)
            return $this;

        if (!(is_array($data) || $data instanceof \Traversable))
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
    function empty()
    {
        foreach($this as $k => $v)
            $this->del($k);

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
}
