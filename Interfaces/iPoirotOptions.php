<?php
namespace Poirot\Core\Interfaces;

use Poirot\Core\AbstractOptions\PropsObject;

interface iPoirotOptions extends iMagicalFields
{
    /**
     * Construct
     *
     * @param array|iPoirotOptions $options Options
     */
    function __construct($options = null);

    /**
     * Set Options
     *
     * @param array|iPoirotOptions $options
     *
     * @return $this
     */
    function from($options);

    /**
     * Set Options From Array
     *
     * @param array $options Options Array
     *
     * @throws \Exception
     * @return $this
     */
    function fromArray(array $options);

    /**
     * Set Options From Same Option Object
     *
     * note: it will take an option object instance of $this
     *       OpenOptions only take OpenOptions as argument
     *
     * - also you can check for private and write_only
     *   methods inside Options Object to get fully coincident copy
     *   of Options Class Object
     *
     * @param iPoirotOptions $options Options Object
     *
     * @throws \Exception
     * @return $this
     */
    function fromOption(iPoirotOptions $options);
    // PHP rise Deceleration Fatal Error even on options
    // that is extend iPoirotOptions interface


    /**
     * Get Properties as array
     *
     * @return array
     */
    function toArray();

    /**
     * Get Options Properties Information
     *
     * @return PropsObject
     */
    function props();
}
