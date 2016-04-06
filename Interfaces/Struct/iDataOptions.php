<?php
namespace Poirot\Std\Interfaces\Struct;

/**
 * Options Is DataStruct Can Be Check Against Fulfillment
 *
 */
interface iDataOptions extends iData
{
    /**
     * Is Required Property Full Filled?
     *
     * - with no property it will check for whole properties
     *
     * @param null|string $property_key
     *
     * @return boolean
     */
    function isFulfilled($property_key = null);
}
