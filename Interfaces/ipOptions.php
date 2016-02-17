<?php
namespace Poirot\Std\Interfaces;

interface ipOptions extends iOptionStruct
{
    /**
     * Construct
     *
     * @param array|iOptionStruct|mixed $options Options
     */
    function __construct($options = null);
}
