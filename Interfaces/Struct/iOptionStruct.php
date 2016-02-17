<?php
namespace Poirot\Std\Interfaces\Struct;

use Poirot\Std\Struct\AbstractOptions\PropsObject;

interface iOptionStruct extends iStructDataConveyor
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
     * @param array|iOptionStruct|mixed $options
     *
     * @return $this
     */
    function from($options);

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
