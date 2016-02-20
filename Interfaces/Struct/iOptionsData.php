<?php
namespace Poirot\Std\Interfaces\Struct;

use Poirot\Std\Struct\AbstractOptions\PropsObject;

interface iOptionsData extends iDataStruct
{
    /**
     * Get Options Properties Information
     *
     * @return PropsObject
     */
    function props();

    /**
     * Is Required Property Full Filled?
     *
     * @param null|string $property_key
     *
     * @return boolean
     */
    function isFulfilled($property_key = null);
}
