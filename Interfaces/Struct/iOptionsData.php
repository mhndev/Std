<?php
namespace Poirot\Std\Interfaces\Struct;

interface iOptionsData extends iDataStruct
{
    /**
     * Is Required Property Full Filled?
     *
     * @param null|string $property_key
     *
     * @return boolean
     */
    function isFulfilled($property_key = null);
}
