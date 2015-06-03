<?php
namespace Poirot\Core\Traits;

!defined('POIROT_CORE_LOADED') and include_once dirname(__FILE__).'/../Core.php';

use Poirot\Core;
use Poirot\Core\Interfaces\EntityInterface;
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
     * SetFrom Resource
     *
     * @var mixed|$this
     */
    protected $_resource;

    /**
     * Construct
     *
     * @param array|iPoirotEntity $props Properties
     *
     * @throws \Exception
     */
    function __construct($props = null)
    {
        if ($props)
            $this->from($props);
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
     * Set Entity From A Given Resource
     *
     * - you have to set resource internally that can given
     *   by getResource method later
     *
     * @param mixed $resource
     *
     * @return $this
     */

    function from($resource)
    {
        $this->_resource = $resource;

        $resource = $this->__setFrom($resource);
        $this->fromArray($resource);

        return $this;
    }

    /**
     * Set Properties
     *
     * - You can implement this method on subclasses
     *
     * @param EntityInterface $resource
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function __setFrom($resource)
    {
        $this->__validateProps($resource);

        if ($resource instanceof $this)
            $resource = $resource->borrow();

        if ($resource instanceof iPoirotEntity)
            $resource = $resource->getAs(new self)->borrow();

        return $resource;
    }

    protected function __validateProps($resource)
    {
        if (!$resource instanceof iPoirotEntity && !is_array($resource))
            throw new \InvalidArgumentException(sprintf(
                'Resource must be instance of EntityInterface or array but "%s" given.'
                , is_object($resource) ? get_class($resource) : gettype($resource)
            ));
    }

    /**
     * Get Resource Data
     *
     * - if no resource data was set on from(method)
     *   return $this
     *
     * @return mixed|$this
     */
    function getResource()
    {
        if (!$this->_resource)
            $this->_resource = $this;

        return $this->_resource;
    }

    /**
     * Merge/Set Data With Entity
     *
     * @param EntityInterface $entity Merge Entity
     *
     * @return $this
     */
    public function merge(EntityInterface $entity)
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
     * @return iPoirotEntity
     */
    function getAs(iPoirotEntity $entity)
    {
        return $entity->from($this)
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
        return $this->toArray();
    }

    // Implement Data Conveyor:

    /**
     * Set Options From Array
     *
     * @param array $options Options Array
     *
     * @throws \Exception
     * @return $this
     */
    function fromArray(array $options)
    {
        foreach ($this->keys() as $key)
            // Delete All Currently Properties
            $this->del($key);

        foreach($options as $key => $val)
            $this->set($key, $val);
    }

    /**
     * Get Properties as array
     *
     * @return array
     */
    function toArray()
    {
        return $this->properties;
    }
} 
