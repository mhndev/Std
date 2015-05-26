<?php
namespace Poirot\Core\Traits;

use Poirot\Core;
use Poirot\Core\AbstractOptions\PropsObject;

trait OpenOptionsTrait
{
    use OptionsTrait;

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
        $propKeys = array_keys($this->properties);

        return new PropsObject([
            'set' => $propKeys,
            'get' => $propKeys
        ]);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function __set($key, $value)
    {
        $this->properties[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    function __get($key)
    {
        return isset($this->properties[$key])
            ? $this->properties[$key]
            : null;
    }

    /**
     * @param string $key
     * @return bool
     */
    function __isset($key)
    {
        return isset($this->properties[$key]);
    }

    /**
     * @param string $key
     * @return void
     */
    function __unset($key)
    {
        if ($this->__isset($key))
            unset($this->properties[$key]);
    }
}
 