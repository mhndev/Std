<?php
namespace Poirot\Core;

abstract class Options implements Interfaces\FieldMagicalInterface
{
    // option_name
    # full access
       # writeonly
    // function setOptionName($val);
       # readonly
    // function getOptionName();

    const PROPERTY_READONLY  = 01;
    const PROPERTY_WRITEONLY = 010;

    /**
     * Construct
     *
     * @param array $options Options Array
     */
    function __construct(array $options = [])
    {
        if (array_values($options) == $options)
            throw new \InvalidArgumentException('Options Array must be associative array.');

        foreach($options as $key => $val)
            $this->$key = $val;
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
        if (method_exists($this, $setter))
            $this->$setter($value);
        elseif ($this->__isset($key))
            throw new \Exception(
                sprintf('The Property "%s" is readonly.', $key)
            );
        else throw new \Exception(
            sprintf('The Property "%s" is not found.', $key)
        );
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
        if (method_exists($this, $getter))
            return $this->$getter();
        elseif (method_exists($this, 'set' . sanitize_camelcase($key)))
            throw new \Exception(
                sprintf('The Property "%s" is writeonly.', $key)
            );
        else throw new \Exception(
                sprintf('The Property "%s" is not found.', $key)
            );
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        $return = true;

        try {
            $this->$key;
        } catch (\Exception $e)
        {
            $return = false;
        }

        return $return;
    }

    /**
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->$key = null;
    }

    function keys()
    {
        // @todo Implement Keys
    }

    function has($key)
    {
        /** @todo Implement Has Property
                  Return self::PROPERTY_* accurding to case
         **/
    }
}
