<?php
namespace Poirot\Std\Interfaces;

interface iPoirotEntity extends EntityInterface
{
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
