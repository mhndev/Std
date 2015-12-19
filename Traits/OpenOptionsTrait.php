<?php
namespace Poirot\Core\Traits;

use Poirot\Core;
use Poirot\Core\AbstractOptions\PropsObject;

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
        $name = strtolower(Core\sanitize_underscore($name));

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
            case 'get':
                $return = $this->__get($name);
                break;
        }

        return $return;
    }

    /**
     * Get Options Properties Information
     *
     * @return PropsObject
     */
    function props()
    {
        $methodProps  = (array) $this->_t__props();
        $methodProps  = ($propKeys = array_keys($this->properties))
            ? \Poirot\Core\array_merge($methodProps, [
                'writable' => $propKeys,
                'readable' => $propKeys
            ])
            : $methodProps;

        return new PropsObject($methodProps);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function __set($key, $value)
    {
        if ($setter = $this->_getSetterIfHas($key))
            ## using setter method
            $this->$setter($value);
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
        if ($getter = $this->_getGetterIfHas($key))
            ## get from getter method
            $return = $this->$getter();
        elseif (array_key_exists($key, $this->properties))
            $return = $this->properties[$key];
        else throw new \Exception(sprintf(
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
            $this->__set($key, null);
        else {
            if (array_key_exists($key, $this->properties))
                unset($this->properties[$key]);
        }
    }
}
