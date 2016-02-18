<?php
namespace Poirot\Std\Struct\Traits;

!defined('POIROT_CORE_LOADED') and include_once dirname(__FILE__) . '/../functions.php';

use Poirot\Std;
use Poirot\Std\Interfaces\Struct\iDataStruct;
use Poirot\Std\Interfaces\ipEntity;

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

    protected $__mapedPropObjects = [
        # 'hash_string' => $none_string_property_value
    ];


    /**
     * Set Entity From A Given Resource
     *
     * - you have to set resource internally that can given
     *   by getResource method later
     *
     * @param mixed $resource
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function from($resource)
    {
        $this->__validateProps($resource);

        $resource = $this->__setFrom($resource);

        if (is_array($resource))
            $this->fromArray($resource);

        return $this;
    }

    /**
     * Set Properties
     *
     * - You can implement this method on subclasses
     *
     * @param mixed $resource
     *
     * @throws \InvalidArgumentException
     * @return array|void
     */
    protected function __setFrom($resource)
    {
        if ($resource instanceof iDataStruct)
            $resource = $resource->toArray();

        return $resource;
    }

    protected function __validateProps($resource)
    {
        if (!$resource instanceof iDataStruct && !is_array($resource))
            throw new \InvalidArgumentException(sprintf(
                'Resource must be instance of EntityInterface or array but "%s" given.'
                , is_object($resource) ? get_class($resource) : gettype($resource)
            ));
    }

    /**
     * Get Property
     * - throw exception if property not found and default get not set
     *
     * @param mixed      $prop    Property name
     * @param null|mixed $default Default Value if not exists
     *
     * @throws \Exception
     * @return mixed
     */
    function get($prop, $default = '__not_set_value__')
    {
        if (!is_string($prop) && !is_numeric($prop))
            $prop = $this->__hashNoneStringProp($prop);


        // avoid recursive trait call, may conflict on classes that
        // implement in this case has() method
        if (!array_key_exists($prop, $this->attainDataArrayObject())
            && $default === self::$__not_set_value__
        )
            throw new \Exception(
                sprintf('Property "%s" not found in entity.', $prop)
            );

        return (array_key_exists($prop, $this->attainDataArrayObject()))
            ? $this->attainDataArrayObject()[$prop]
            : $default;
    }

    /**
     * Set Property with value
     *
     * @param mixed $prop  Property
     * @param mixed $value Value
     *
     * @return $this
     */
    function set($prop, $value = '__not_set_value__')
    {
        if (!is_string($prop) && !is_numeric($prop)) {
            $propObj = $prop;
            $prop    = $this->__hashNoneStringProp($prop);

            ## store object map
            $this->__mapedPropObjects[$prop] = $propObj;
        }

        if ($value === self::$__not_set_value__)
            $value = self::$DEFAULT_NONE_VALUE;

        $this->attainDataArrayObject()[$prop] = $value;

        return $this;
    }

        protected function __hashNoneStringProp($prop)
        {
            if(is_object($prop))
                return spl_object_hash($prop);
            else
                return md5(serialize($prop));
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
        if (!is_string($prop) && !is_numeric($prop))
            $prop = $this->__hashNoneStringProp($prop);

        return array_key_exists($prop, $this->attainDataArrayObject());
    }

    /**
     * Is Entity Empty?
     *
     * @return boolean
     */
    function isEmpty()
    {
        return empty($this->attainDataArrayObject());
    }

    /**
     * Empty Entity Data
     *
     * @return $this
     */
    function clean()
    {
        foreach($this->keys() as $key)
            $this->del($key);

        return $this;
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
        if (!is_string($prop) && !is_numeric($prop)) {
            $prop = $this->__hashNoneStringProp($prop);
            unset($this->__mapedPropObjects[$prop]);
        }


        if ( array_key_exists($prop, $this->attainDataArrayObject()) )
            unset($this->attainDataArrayObject()[$prop]);

        return $this;
    }

    /**
     * Get All Properties Keys
     *
     * @return array
     */
    function keys()
    {
        $keys = [];
        $data = $this->attainDataArrayObject();
        if (!$data)
            return [];

        foreach(array_keys($data) as $k) {
            if (array_key_exists($k, $this->__mapedPropObjects))
                $k = $this->__mapedPropObjects[$k];

            $keys[] = $k;
        }

        return $keys;
    }

    /**
     * Get a copy of properties as hydrate structure
     *
     * @param ipEntity $entity Entity
     *
     * @return ipEntity
     */
    function getAs(ipEntity $entity)
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
     * - don't delete current entity
     *
     * @param array $options Options Array
     *
     * @throws \Exception
     * @return $this
     */
    function fromArray(array $options)
    {
        foreach($options as $key => $val)
            $this->set($key, $val);

        return $this;
    }

    /**
     * Get Properties as array
     *
     * @return array
     */
    function toArray()
    {
        $properties = $this->attainDataArrayObject();
        if (!$properties)
            $properties = [];

        return $properties;
    }

    /**
     * @return array
     */
    protected function &attainDataArrayObject()
    {
        return $this->properties;
    }
}
