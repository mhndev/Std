<?php
namespace Poirot\Std\Interfaces;

use Poirot\Std\Interfaces\Struct\iOptionStruct;

interface ipOptions extends iOptionStruct
{
    /**
     * Construct
     *
     * @param array|iOptionStruct|mixed $options Options
     */
    function __construct($options = null);
}
