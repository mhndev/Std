<?php
namespace Poirot\Std\Interfaces\Struct;

/**
 * class DataField()
 * {
 *    public $field;
 *
 * }
 *
 * DataField->field = 1;
 *
 */
interface iMeanStruct
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function __set($key, $value);

    /**
     * @param string $key
     * @return mixed
     */
    function __get($key);

    /**
     * @param string $key
     * @return bool
     */
    function __isset($key);

    /**
     * @param string $key
     * @return void
     */
    function __unset($key);
}
