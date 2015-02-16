<?php
namespace Poirot\Core\Interfaces;

interface iPoirotEntity extends EntityInterface
{
    /**
     * Set Properties From Entity Object
     *
     * @param iPoirotEntity $entity
     *
     * @return $this
     */
    function setFrom(iPoirotEntity $entity);

    /**
     * Merge/Set Data With Entity
     *
     * @param iPoirotEntity $entity Merge Entity
     *
     * @return $this
     */
    function merge(iPoirotEntity $entity);

    /**
     * Get a copy of properties as hydrate structure
     *
     * @param iPoirotEntity $entity Entity
     *
     * @return mixed
     */
    function getAs(iPoirotEntity $entity);

    /**
     * Output Conveyor Props. as desired manipulated data struct.
     *
     * ! Be Aware of the situation for classes that extend Entity
     *   and maybe have stored original properties in the other way
     *   instead of $this->properties in exp. for session storage,
     *   so i prefer use:
     *   [code]
     *      return $this->getAs(new Entity($this));
     *   [/code]
     *
     * @return mixed
     */
    function borrow();
}
