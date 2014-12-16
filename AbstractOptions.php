<?php
namespace Poirot\Core;

use Poirot\Core;

/**
 * Object is the base class that implements the *property* feature.
 *
 * A property is defined by a getter method (e.g. `getLabel`), and/or a setter method (e.g. `setLabel`). For example,
 * the following getter and setter methods define a property named `label`:
 *
 * ~~~
 * private $_label;
 *
 * public function getLabel()
 * {
 *     return $this->_label;
 * }
 *
 * public function setLabel($value)
 * {
 *     $this->_label = $value;
 * }
 * ~~~
 *
 * Property names are *case-insensitive*.
 *
 * A property can be accessed like a member variable of an object. Reading or writing a property will cause the invocation
 * of the corresponding getter or setter method. For example,
 *
 * ~~~
 * // equivalent to $label = $object->getLabel();
 * $label = $object->label;
 * // equivalent to $object->setLabel('abc');
 * $object->label = 'abc';
 * ~~~
 *
 * If a property has only a getter method and has no setter method, it is considered as *read-only*. In this case, trying
 * to modify the property value will cause an exception.
 *
 * One can call [[hasProperty()]], [[canGetProperty()]] and/or [[canSetProperty()]] to check the existence of a property.
 *
 * Besides the property feature, Object also introduces an important object initialization life cycle. In particular,
 * creating an new instance of Object or its derived class will involve the following life cycles sequentially:
 *
 * 1. the class constructor is invoked;
 * 2. object properties are initialized according to the given configuration;
 * 3. the `init()` method is invoked.
 *
 * In the above, both Step 2 and 3 occur at the end of the class constructor. It is recommended that
 * you perform object initialization in the `init()` method because at that stage, the object configuration
 * is already applied.
 *
 * In order to ensure the above life cycles, if a child class of Object needs to override the constructor,
 * it should be done like the following:
 *
 * ~~~
 * public function __construct($param1, $param2, ..., $config = [])
 * {
 *     ...
 *     parent::__construct($config);
 * }
 * ~~~
 *
 * That is, a `$config` parameter (defaults to `[]`) should be declared as the last parameter
 * of the constructor, and the parent implementation should be called at the end of the constructor.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class AbstractOptions implements Interfaces\FieldMagicalInterface
{
    // option_name
    # full access
       # writeonly
    // public function setOptionName($val);
       # readonly
    // public function getOptionName();

    /**
     * Construct
     *
     * @param array $options Options Array
     */
    function __construct(array $options = [])
    {
        if (!empty($options) && array_values($options) == $options)
            throw new \InvalidArgumentException('Options Array must be associative array.');

        foreach($options as $key => $val)
            $this->__set($key, $val);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @throws \Exception
     * @return void
     */
    public function __set($key, $value)
    {
        $setter = 'set' . sanitize_camelcase($key);
        if ($this->isMethodExists($setter))
            $this->$setter($value);
        elseif ($this->__isset($key))
            throw new \Exception(sprintf(
                'The Property "%s" is readonly.'
                , $key
            ));
        else throw new \Exception(sprintf(
            'The Property "%s" not having any Public Setter Method Match.'
            , $key
        ));
    }

    /**
     * @param string $key
     *
     * @throws \Exception
     * @return mixed
     */
    public function __get($key)
    {
        $getter = 'get' . sanitize_camelcase($key);
        if ($this->isMethodExists($getter))
            return $this->$getter();
        elseif ($this->isMethodExists('set' . sanitize_camelcase($key)))
            throw new \Exception(sprintf(
                'The Property "%s" is writeonly.'
                , $key
            ));
        else throw new \Exception(sprintf(
            'The Property "%s" is not found.'
            , $key
        ));
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        try {
            $this->__get($key);
        } catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->__set($key, null);
    }

    /**
     * Get Option Properties Information
     *
     * @return AbstractOptions\PropsObject
     */
    function props()
    {
        $ref     = new \ReflectionClass($this);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        $props   = [];
        foreach($methods as $i => $method)
            if (! in_array($prefix = substr($method->getName(), 0, 3), ['set', 'get']))
                // this is not property method
                unset($methods[$i]);
            else
                $props[$prefix][] = Core\sanitize_underscore(
                    strtolower(str_replace($prefix, '', $method->getName()))
                );

        return new AbstractOptions\PropsObject($props);
    }

    /**
     * Is Setter Property Method?
     *
     * @param string $method Method Name
     *
     * @return bool
     */
    protected function isMethodExists($method)
    {
        $return = method_exists($this, $method);
        if ($return) {
            $ref = new \ReflectionMethod($this, $method);
            $return = $return && $ref->isPublic();
        }

        return $return;
    }
}
