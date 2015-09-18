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
        // Looking for set | get :
        $action  = substr($method, 0, 3);

        // Option Name:
        $name = $method;
        $name = substr($name, -(strlen($name)-3)); // 3 for set/get
        $name = strtolower(Core\sanitize_underscore($name));

        // Take Action:
        $return = null;
        switch ($action) {
            case 'set':
                // init option value:
                if (empty($arguments))
                    throw new \InvalidArgumentException(
                        "Method {$method} need argument as option value."
                    );
                $this->__set($name, $arguments[0]);
                $return = $this;
                break;

            case 'get':
                $return = $this->__get($name);
                break;

            default:
                // It's not an option, set[MethodName]()
        }

        return $return;
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
        $setter = 'set' . Core\sanitize_camelcase($key);
        if ($this->isMethodExists($setter))
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
        $getter = 'get' . Core\sanitize_camelcase($key);
        if ($this->isMethodExists($getter))
            ## get from getter method
            $return = $this->$getter();
        elseif (isset($this->properties[$key]))
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
        $setter = 'set' . Core\sanitize_camelcase($key);
        if ($this->isMethodExists($setter))
            $this->__set($key, null);
        else {
            if (array_key_exists($key, $this->properties))
                unset($this->properties[$key]);
        }
    }
}
