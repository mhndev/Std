<?php
namespace Poirot\Std\Struct;

use Poirot\Std\Interfaces\Struct\iOptionsData;
use Poirot\Std\Struct\AbstractOptions\PropsObject;

class OpenOptionsData extends AbstractOptionsData
    implements iOptionsData
{
    /**
     * @var array
     */
    protected $properties = [];

    /**
     * Proxy for [set/get]Options()
     *
     * @param $method
     * @param $arguments
     *
     * @return bool|mixed|$this
     */
    function __call($method, $arguments)
    {
        $return = null;
        foreach(['set', 'get', 'is'] as $action)
            if (strpos($method, $action) === 0) {
                ## method setter/getter found
                $return = true;
                break;
            }

        if ($return === null)
            ## nothing to do
            return $return;

        // Option Name:
        $name = $method;
        $name = substr($name, -(strlen($name)-strlen($action))); // x for set/get
        $name = $this->__normalize($name, 'external');

        // Take Action:
        switch ($action) {
            case 'set':
                // init option value:
                if (empty($arguments))
                    $arguments[0] = null;

                $this->__set($name, $arguments[0]);
                $return = $this;
                break;

            case 'is':
                // TODO now property can catch with both get[Prop] & is[Prop]
            case 'get':
                $return = $this->__get($name);
                break;
        }

        return $return;
    }

    /**
     * - VOID values will unset attribute
     * @param string $key
     * @param mixed $value
     * @throws \Exception
     */
    function __set($key, $value)
    {
        $key = $this->__normalize($key, 'external');

        if ($setter = $this->_getSetterIfHas($key))
            ## using setter method
            $this->$setter($value);

        if (in_array('set'.$this->__normalize($key, 'internal'), $this->doWhichMethodIgnored()))
            throw new \Exception(sprintf(
                'The Property (%s) is not Writable.'
                , $key
            ));

        if ($value === null)
            unset($this->properties[$key]);
        else
            $this->properties[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @throws \Exception
     * @return mixed
     */
    function __get($key)
    {
        $key = $this->__normalize($key, 'external');

        $return = null;
        if ($getter = $this->_getGetterIfHas($key))
            ## get from getter method
            $return = $this->$getter();
        elseif (array_key_exists($key, $this->properties)
            ## not ignored
            && !in_array('get'.$this->__normalize($key, 'internal'), $this->doWhichMethodIgnored())
        )
            $return = $this->properties[$key];

        if ($return === null)
            throw new \Exception(sprintf(
                'The Property "%s" is not found.'
                , $key
            ));

        return $return;
    }

    /**
     * @param string $key
     * @return void
     */
    function __unset($key)
    {
        $key = $this->__normalize($key, 'external');

        if ($setter = $this->_getSetterIfHas($key))
            try{
                ## some times it can be set to null because of argument type definition
                $this->__set($key, null);
            } catch (\Exception $e) {}
        else {
            if (array_key_exists($key, $this->properties))
                unset($this->properties[$key]);
        }
    }

    /**
     * Get Options Properties Information
     *
     */
    protected function __props()
    {
        $methodProps  = parent::__props();


        $props = [];

        // Methods as Options:
        foreach($methodProps as $p)
            $props[$p->getKey()] = $p;

        // Property Open Options:
        foreach(array_keys($this->properties) as $propertyName)
        {
            foreach(['set', 'get', 'is'] as $prefix) {
                # check for ignorant
                $method = $prefix . $this->__normalize($propertyName, 'internal');
                if (in_array($method, $this->doWhichMethodIgnored()))
                    ## it will use as internal option method
                    continue;

                // mark readable/writable for property
                (isset($props[$propertyName])) ?: $props[$propertyName] = new PropsObject($propertyName);
                ($prefix == 'set')
                    ? $props[$propertyName]->setWritable()
                    : $props[$propertyName]->setReadable()
                ;
            }
        }

        // Yield Combination:
        foreach($props as $p)
            yield $p;
    }
}
