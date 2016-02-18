<?php
namespace Poirot\Std\Struct;

use Traversable;

/*
$mean = new DataField();
$mean->{ (string) this will converted to string by php };

$mean->test = [];

$test = &$mean->test;  // called with & sign
var_dump($test);            // array(0) { }
$test[] = 'insert item';    // if called with & now $test is reference of $mean->test
var_dump($test);            // array(1) { [0]=> string(11) "insert item" }
var_dump($mean->test); // array(1) { [0]=> string(11) "insert item" }
*/

class Mean extends AbstractStruct
{
    protected $properties = [];

    /**
     * Set Struct Data From Array
     *
     * @param array|\Traversable $data
     */
    function doSetFrom($data)
    {
        foreach($data as $k => $v)
            $this->__set($k, $v);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        foreach(array_keys($this->properties) as $key)
            yield $key => $this->__get($key);
    }


    // Mean Implementation:

    /**
     * NULL value for a property considered __isset false
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function __set($key, $value)
    {
        if ($value === null)
            return $this->__unset($key);

        $this->properties[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    function &__get($key)
    {
        if (!$this->__isset($key))
            $this->properties[$key] = null;

        $x = &$this->properties[$key];
        return $x;
    }

    /**
     * NULL value for a property considered __isset false
     * @param string $key
     * @return bool
     */
    function __isset($key)
    {
        return (array_key_exists($key, $this->properties)) && $this->properties[$key] !== null;
    }

    /**
     * NULL value for a property considered __isset false
     * @param string $key
     * @return void
     */
    function __unset($key)
    {
        if ($this->__isset($key))
            unset($this->properties[$key]);
    }
}
