<?php
namespace Poirot\Std\Interfaces\Struct;

use Poirot\Std\Struct\AbstractOptions\PropsObject;

interface iOptionDataStruct extends iDataStruct
{
    /**
     * Set Options
     *
     * !! set options from same(instance of this) option object
     *    so we can check for private and write_only methods
     *    inside Options Object to get fully coincident copy
     *    of Options Class Object
     *
     *
     * @param array|iOptionDataStruct|mixed $data
     *
     * @return $this
     */
    function from($data);

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

    /**
     * Clear All Property Data
     *
     * - value of each property is VOID
     *
     * @return void
     */
    function clear();

    /**
     * Has no property defined and is clear?
     * @return bool
     */
    function isEmpty();
}
