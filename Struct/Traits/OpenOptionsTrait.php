<?php
namespace Poirot\Std\Struct\Traits;

use Poirot\Std;
use Poirot\Std\Struct\AbstractOptions\PropsObject;

/*
 * $openOption = new class OpenOptions {
 *    use OpenOptionsTrait;
 *
 *    function setWritableOption($value) {
 *       // ...
 *       // we can set variable into $this->properties turn key/value
 *       // through readable props
 *    }
 * }
 *
 * $openOption->setAnonymousOption('open option value');
 *
 * print_r($openOption->props());
 *
 * // ['complex'  => ['writable_option', 'anonymous_option'],
 * //  'readable' => ['anonymous_option'],
 * //  'writable' => ['writable_option', 'anonymous_option'],
 * // ]
 *
 */

trait OpenOptionsTrait
{
    use OptionsTrait {
        OptionsTrait::props as protected _t__props;
    }

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
        if ($setter = $this->_getSetterIfHas($key))
            ## using setter method
            $this->$setter($value);

        if (in_array('set'.$this->__normalize($key, 'internal'), $this->doWhichMethodIgnored()))
            throw new \Exception(sprintf(
                'The Property "%s" is writeonly.'
                , $key
            ));

        if ($value === VOID)
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
        $return = VOID;
        if ($getter = $this->_getGetterIfHas($key))
            ## get from getter method
            $return = $this->$getter();
        elseif (array_key_exists($key, $this->properties)
            ## not ignored
            && !in_array('get'.$this->__normalize($key, 'internal'), $this->doWhichMethodIgnored())
        )
            $return = $this->properties[$key];

        if ($return === VOID)
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
        if ($setter = $this->_getSetterIfHas($key))
            try{
                ## some times it can be set to null because of argument type definition
                $this->__set($key, VOID);
            } catch (\Exception $e) {}
        else {
            if (array_key_exists($key, $this->properties))
                unset($this->properties[$key]);
        }
    }

    /**
     * Get Options Properties Information
     *
     * @return PropsObject
     */
    function props()
    {
        $methodProps  = (array) $this->_t__props();

        $writable = [];
        $readable = [];
        foreach(array_keys($this->properties) as $key) {
            $skip = [];
            foreach(['set', 'get', 'is'] as $prefix) {
                # check for ignorant
                $method = $prefix . $this->__normalize($key, 'internal');
                if (in_array($method, $this->doWhichMethodIgnored()))
                    ## it will use as internal option method
                    $skip[] = $prefix;
            }

            if (!in_array('get', $skip) && !in_array('is', $skip))
                $readable[] = $key;

            if (!in_array('set', $skip))
                $writable[] = $key;
        }

        $methodProps  = \Poirot\Std\array_merge($methodProps
            , [
                'writable' => $writable,
                'readable' => $readable
            ]);

        return new PropsObject($methodProps);
    }
}
