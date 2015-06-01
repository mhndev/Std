<?php
namespace Poirot\Core\Traits;

!defined('POIROT_CORE_LOADED') and include_once dirname(__FILE__).'/../Core.php';

use Poirot\Core;
use Poirot\Core\Interfaces\iPoirotEntity;

trait EntityTrait
{
    static $DEFAULT_NONE_VALUE = null;

    // to detect get() default if not set
    static $__not_set_value__ = '__not_set_value__';

    /**
     * Entity's items
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Construct
     *
     * @param array|iPoirotEntity $props Properties
     *
     * @throws \Exception
     */
    function __construct($props = null)
    {
        if ($props) {
            if ($props instanceof iPoirotEntity)
                $props = $props->getAs(new self);

            if (!is_array($props))
                throw new \Exception(sprintf(
                    'Properties must instance of "Entity" or "Array" but "%s" given.',
                    is_object($props) ? get_class($props) : gettype($props)
                ));

            if (!empty($this->properties))
                // maybe we have some predefined props field in class
                // protected properties = array( .... );
                $props = Core\array_merge($this->properties, $props);

            foreach($props as $key => $val)
                $this->set($key, $val);
        }
    }

    /**
     * Get Property
     * - throw exception if property not found and default get not set
     *
     * @param string     $prop    Property name
     * @param null|mixed $default Default Value if not exists
     *
     * @throws \Exception
     * @return mixed
     */
    function get($prop, $default = '__not_set_value__')
    {
        // avoid recursive trait call, may conflict on classes that
        // implement in this case has() method
        #if (!$this->has($prop) && $default === self::$__not_set_value__)
        if (!array_key_exists($prop, $this->properties)
            && $default === self::$__not_set_value__
        )
            throw new \Exception(
                sprintf('Property "%s" not found in entity.', $prop)
            );

        return (array_key_exists($prop, $this->properties))
            ? $this->properties[$prop]
            : $default;
    }

    /**
     * Set Property with value
     *
     * @param string $prop  Property
     * @param mixed  $value Value
     *
     * @return $this
     */
    function set($prop, $value = '__not_set_value__')
    {
        if ($value === self::$__not_set_value__)
            $value = self::$DEFAULT_NONE_VALUE;

        $this->properties[$prop] = $value;

        return $this;
    }

    /**
     * Set Properties
     *
     * - by deleting existence properties
     *
     * @param iPoirotEntity $entity
     *
     * @return $this
     */
    function setFrom(iPoirotEntity $entity)
    {
        foreach ($this->keys() as $key)
            // Delete All Currently Properties
            $this->del($key);

        $this->merge($entity);

        return $this;
    }

    /**
     * Merge/Set Data With Entity
     *
     * @param iPoirotEntity $entity Merge Entity
     *
     * @return $this
     */
    function merge(iPoirotEntity $entity)
    {
        foreach($entity->keys() as $key)
            $this->set($key, $entity->get($key));

        return $this;
    }

    /**
     * Has Property
     *
     * @param string $prop Property
     *
     * @return boolean
     */
    function has($prop)
    {
        return array_key_exists($prop, $this->properties);
    }

    /**
     * Delete a property
     *
     * @param string $prop Property
     *
     * @return $this
     */
    function del($prop)
    {
        if (array_key_exists($prop, $this->properties))
            unset($this->properties[$prop]);

        return $this;
    }

    /**
     * Get All Properties Keys
     *
     * @return array
     */
    function keys()
    {
        return array_keys($this->properties);
    }

    /**
     * Get a copy of properties as hydrate structure
     *
     * @param iPoirotEntity $entity Entity
     *
     * @return mixed
     */
    function getAs(iPoirotEntity $entity)
    {
        return $entity->setFrom($this)
            ->borrow();
    }

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
    function borrow()
    {
        return $this->properties;
    }
} 