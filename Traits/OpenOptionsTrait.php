<?php
namespace Poirot\Core\Traits;

use Poirot\Core\AbstractOptions\PropsObject;
use Poirot\Core\Interfaces\iOptionImplement;

trait OpenOptionsTrait
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
        // Looking for set | get :
        $action  = substr($method, 0, 3);

        // Option Name:
        $name = $method;
        $name = substr($name, -(strlen($name)-3)); // 3 for set/get
        $name = strtolower(\Poirot\Core\sanitize_underscore($name));

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
     * Set Options
     *
     * @param array|iOptionImplement $options
     *
     * @return $this
     */
    function from($options)
    {
        if (is_array($options))
            $this->fromArray($options);
        elseif ($options instanceof iOptionImplement)
            $this->fromOption($options);

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
        if (empty($options))
            return $this;

        if (array_values($options) == $options)
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
     * @param iOptionImplement $options Options Object
     *
     * @throws \Exception
     * @return $this
     */
    function fromOption(iOptionImplement $options)
    {
        if (!$options instanceof $this)
            // only get same option object
            throw new \Exception(sprintf(
                'Given Options Is Not Same As Provided Class Options. you given "%s".'
                , get_class($options)
            ));

        foreach($options->props()->writable as $key)
            $this->__set($key, $options->{$key});

        return $this;
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
 