<?php
namespace Poirot\Core\Interfaces;

interface iEntityPoirot extends EntityInterface
{
    /**
     * Set Properties From Entity Object
     *
     * @param iEntityPoirot $entity
     *
     * @return $this
     */
    function setFrom(iEntityPoirot $entity);

    /**
     * Merge/Set Data With Entity
     *
     * @param iEntityPoirot $entity Merge Entity
     *
     * @return $this
     */
    function merge(iEntityPoirot $entity);

    /**
     * Get a copy of properties as hydrate structure
     *
     * @param iEntityPoirot $entity Entity
     *
     * @return mixed
     */
    function getAs(iEntityPoirot $entity);

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
