<?php
namespace Poirot\Core\Interfaces;

/**
 * Objects that implement this interface can interchange data
 * provided with each others
 *
 */
interface iDataSetConveyor 
{
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
     * Get Properties as array
     *
     * @return array
     */
    function toArray();
}
