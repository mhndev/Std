<?php
namespace Poirot\Core\Interfaces;

use Poirot\Core\AbstractOptions\PropsObject;

interface iOptionImplement extends iMagicalFields, iDataSetConveyor
{
    /**
     * Set Options
     *
     * @param array|iPoirotOptions|mixed $options
     *
     * @return $this
     */
    function from($options);

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
     * @param iOptionImplement $options Options Object
     *
     * @throws \Exception
     * @return $this
     */
    function fromSimilar(/*iOptionImplement*/ $options);
    // PHP rise Deceleration Fatal Error even on options
    // that is extend iPoirotOptions interface

    /**
     * Get Options Properties Information
     *
     * @return PropsObject
     */
    function props();
}
