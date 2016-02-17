<?php
namespace Poirot\Std\Struct;

use Poirot\Std\Interfaces\Struct\iMeanStruct;

/*
$dataField = new DataField();
$dataField->test = [];

$test = &$dataField->test;  // called with & sign
var_dump($test);            // array(0) { }
$test[] = 'insert item';    // if called with & now $test is reference of $dataField->test
var_dump($test);            // array(1) { [0]=> string(11) "insert item" }
var_dump($dataField->test); // array(1) { [0]=> string(11) "insert item" }
*/

class Mean implements iMeanStruct
{
    protected $properties = [];

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function __set($key, $value)
    {
        $this->properties[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    function &__get($key)
    {
        if (!$this->__isset($key)) {
            $this->properties[$key] = null;
        }

        $x = &$this->properties[$key];
        return $x;
    }

    /**
     * @param string $key
     * @return bool
     */
    function __isset($key)
    {
        return array_key_exists($key, $this->properties);
    }

    /**
     * @param string $key
     * @return void
     */
    function __unset($key)
    {
        if ($this->__isset($key))
            unset($this->properties[$key]);
    }
}
