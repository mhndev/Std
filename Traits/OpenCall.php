<?php
namespace Poirot\Core\Traits;

trait OpenCall
{
    private $_t__methods = [];

    function addMethod($methodName, $methodCallable)
    {
        if (!is_callable($methodCallable))
            throw new \InvalidArgumentException('Second param must be callable');

        $this->_t__methods[$methodName] = \Closure::bind($methodCallable, $this, get_class());
    }

    function __call($methodName, array $args)
    {
        if (isset($this->_t__methods[$methodName]))
            return call_user_func_array($this->_t__methods[$methodName], $args);

        throw new \RunTimeException('There is no method with the given name to call');
    }
}
