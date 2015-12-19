<?php
namespace Poirot\Core\Traits;

use Poirot\Core;
use Poirot\Core\AbstractOptions\PropsObject;
use Poirot\Core\Interfaces\iDataSetConveyor;
use Poirot\Core\Interfaces\iOptionImplement;

trait OptionsTrait
{
    /**
     * @var PropsObject Cached Props Once Call props()
     */
    protected $_cachedProps;

    /**
     * Set Options
     *
     * @param array|iOptionImplement $options
     *
     * @return $this
     */
    function from($options)
    {
        if ($options instanceof iDataSetConveyor)
            $options = $options->toArray();

        if (is_array($options))
            $this->fromArray($options);
        elseif ($options instanceof $this)
            $this->fromSimilar($options);

        return $this;
    }

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
        if (!empty($options) && array_values($options) === $options)
            throw new \InvalidArgumentException('Options Array must be associative array.');

        foreach($options as $key => $val)
            $this->__set($key, $val);

        return $this;
    }

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
     * @param iOptionImplement $context Options Object
     *
     * @throws \Exception
     * @return $this
     */
    function fromSimilar(/*iOptionImplement*/ $context)
    {
        if ($context instanceof iOptionImplement) {
            foreach($context->props()->readable as $key)
                $this->__set($key, $context->__get($key));

            return $this;
        }

        if (!$context instanceof $this)
            // only get same option object
            /*throw new \Exception(sprintf(
                'Given Options Is Not Same As Provided Class Options. you given "%s".'
                , get_class($options)
            ));*/

            foreach($context->props()->writable as $key)
                $this->__set($key, $context->__get($key));

        // call your inherit options actions:
        // maybe you want access protected methods or properties
        // ...

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @throws \Exception
     * @return void
     */
    function __set($key, $value)
    {
        if ($setter = $this->_getSetterIfHas($key))
            $this->$setter($value);
        elseif ($this->__isset($key))
            throw new \Exception(sprintf(
                'The Property "%s" is readonly.'
                , $key
            ));
        else throw new \Exception(sprintf(
            'The Property (%s) not having any Public Setter Method Match on (%s).'
            , $key, get_class($this)
        ));
    }

    /**
     * @param string $key
     *
     * @throws \Exception
     * @return mixed
     */
    function __get($key)
    {
        if ($getter = $this->_getGetterIfHas($key))
            return $this->$getter();
        elseif ($this->_isMethodExists('set' . Core\sanitize_camelcase($key)))
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
    function __isset($key)
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
    function __unset($key)
    {
        $this->__set($key, null);
    }

    /**
     * Get Options Properties Information
     *
     * @return PropsObject
     */
    function props()
    {
        if ($this->_cachedProps)
            return $this->_cachedProps;

        $ref     = new \ReflectionClass($this);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        $props   = [];
        foreach($methods as $i => $method) {
            foreach(['set', 'get', 'is'] as $prefix)
                if (strpos($method->getName(), $prefix) === 0)
                    ## set --> props['writable']
                    $props[($prefix == 'set') ? 'writable' : 'readable'][] = strtolower(Core\sanitize_underscore(
                        str_replace($prefix, '', $method->getName())
                    ));
        }

        return $this->_cachedProps = new PropsObject($props);
    }

    /**
     * Get Properties as array
     *
     * @return array
     */
    function toArray()
    {
        $rArray = [];
        foreach($this->props()->readable as $p)
            $rArray[$p] = $this->__get($p);

        return $rArray;
    }

    // ...

    protected function _getGetterIfHas($key, $prefix = 'get')
    {
        $getter = $prefix . Core\sanitize_camelcase($key);
        if (! ( $result = $this->_isMethodExists($getter) ) && $prefix === 'get')
            return $this->_getGetterIfHas($key, 'is');

        return ($result) ? $getter : false;
    }

    protected function _getSetterIfHas($key)
    {
        $setter = 'set' . Core\sanitize_camelcase($key);
        return ($this->_isMethodExists($setter)) ? $setter : false;
    }

    /**
     * Is Setter Property Method?
     *
     * @param string $method Method Name
     *
     * @return bool
     */
    protected function _isMethodExists($method)
    {
        $return = method_exists($this, $method);
        if ($return) {
            $ref = new \ReflectionMethod($this, $method);
            $return = $return && $ref->isPublic();
        }

        return $return;
    }
}
 