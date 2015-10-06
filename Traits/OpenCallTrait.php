<?php
namespace Poirot\Core\Traits;

trait OpenCallTrait
{
    private $_t__methods = [];
    private $_t__bindTo;

    /**
     * Bind Current Methods to Given Object
     *
     * @param $class
     *
     * @throws \Exception
     * @return $this
     */
    function bindTo($class)
    {
        if (!is_object($class))
            throw new \Exception(sprintf(
                'Given class must be an object (%s) given.'
                , \Poirot\Core\flatten($class)
            ));

        $this->_t__bindTo = $class;

        return $this;
    }

    /**
     * Get Given Bind Object
     *
     * @return object
     */
    function getBindTo()
    {
        if (!$this->_t__bindTo)
            $this->bindTo($this);

        return $this->_t__bindTo;
    }

    /**
     * Attach Method To This Class
     *
     * @param string   $methodName
     * @param \Closure $methodCallable
     *
     * @return $this
     */
    function addMethod($methodName, \Closure $methodCallable)
    {
        $this->_t__methods[$methodName] = $methodCallable;

        return $this;
    }

    /**
     * Has Method Name Exists?
     *
     * @param string $methodName
     *
     * @return bool
     */
    function hasMethod($methodName)
    {
        return isset($this->_t__methods[$methodName]);
    }

    /**
     * Proxy Call To Registered Methods
     *
     * @param $methodName
     * @param array $args
     *
     * @return mixed
     */
    function __call($methodName, array $args)
    {
        if (!$this->hasMethod($methodName))
            throw new \RunTimeException('There is no method with the given name to call');

        $methodCallable = $this->_t__methods[$methodName];
        ## bind it into latest bind object
        $methodCallable = \Closure::bind(
            $methodCallable
            , $this->getBindTo()
            , get_class($this->getBindTo())
        );

        return call_user_func_array($methodCallable, $args);
    }
}
