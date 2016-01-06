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
                // TODO just for boolean values
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

        $writable = [];
        $readable = [];
        foreach(array_keys($this->properties) as $key) {
            $skip = [];
            foreach(['set', 'get', 'is'] as $prefix) {
                # check for ignorant
                $method = $prefix . Core\sanitize_camelcase($key);
                if (in_array($method, $this->_t_options__internal))
                    ## it will use as internal option method
                    $skip[] = $prefix;
            }

            if (!in_array('get', $skip) && !in_array('is', $skip))
                $readable[] = $key;

            if (!in_array('set', $skip))
                $writable[] = $key;
        }

        $methodProps  = \Poirot\Core\array_merge($methodProps
            , [
                'writable' => $writable,
                'readable' => $readable
            ]);

        return new PropsObject($methodProps);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @throws \Exception
     */
    function __set($key, $value)
    {
        if ($setter = $this->_getSetterIfHas($key))
            ## using setter method
            $this->$setter($value);

        if (in_array('set'.Core\sanitize_camelcase($key), $this->_t_options__internal))
            throw new \Exception(sprintf(
                'The Property "%s" is writeonly.'
                , $key
            ));

        $this->properties[$key] = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    function __isset($key)
    {
        return ($this->_getGetterIfHas($key) !== false || array_key_exists($key, $this->properties));
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
        elseif (array_key_exists($key, $this->properties)
            && !in_array('get'.Core\sanitize_camelcase($key), $this->_t_options__internal)
        )
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
            try{
                ## some times it can be set to null because of argument type definition
                $this->__set($key, null);
            } catch (\Exception $e) {}
        else {
            if (array_key_exists($key, $this->properties))
                unset($this->properties[$key]);
        }
    }
}
