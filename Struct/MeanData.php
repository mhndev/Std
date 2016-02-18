<?php
namespace Poirot\Std\Struct;

use Traversable;

/*
$mean = new MeanData();
$mean->{ (string) this will converted to string by php };

$mean->test = [];

$test = &$mean->test;  // called with & sign
var_dump($test);            // array(0) { }
$test[] = 'insert item';    // if called with & now $test is reference of $mean->test
var_dump($test);            // array(1) { [0]=> string(11) "insert item" }
var_dump($mean->test); // array(1) { [0]=> string(11) "insert item" }
*/

class MeanData extends AbstractDataStruct
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
            return $this->del($key);

        $this->properties[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    function &__get($key)
    {
        if (!$this->has($key))
            $this->properties[$key] = null;

        $x = &$this->properties[$key];
        return $x;
    }

    /**
     * NULL value for a property considered __isset false
     * @param string $key
     * @return bool
     */
    function has($key)
    {
        return (array_key_exists($key, $this->properties)) && $this->properties[$key] !== null;
    }

    /**
     * NULL value for a property considered __isset false
     * @param string $key
     * @return bool
     */
    function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * NULL value for a property considered __isset false
     * @param string $key
     * @return void
     */
    function del($key)
    {
        if ($this->__isset($key))
            unset($this->properties[$key]);
    }

    /**
     * NULL value for a property considered __isset false
     * @param string $key
     * @return void
     */
    function __unset($key)
    {
        $this->del($key);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->properties);
    }
}
