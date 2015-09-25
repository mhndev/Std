<?php
namespace Poirot\Core\Traits;

trait OpenCallTrait
{
    private $_t__methods = [];
    private $_t__bindto;

    function bindTo($class)
    {
        if (!is_object($class))
            throw new \Exception('Class is not object.');

        $this->_t__bindto = $class;
    }

    function getBindTo()
    {
        if (!$this->_t__bindto)
            $this->bindTo($this);

        return $this->_t__bindto;
    }

    function addMethod($methodName, $methodCallable)
    {
        if (!is_callable($methodCallable))
            throw new \InvalidArgumentException('Second param must be callable');

        $this->_t__methods[$methodName] = \Closure::bind(
            $methodCallable
            , $this->getBindTo()
            , get_class($this->getBindTo())
        );
    }

    function __call($methodName, array $args)
    {
        if (isset($this->_t__methods[$methodName]))
            return call_user_func_array($this->_t__methods[$methodName], $args);

        throw new \RunTimeException('There is no method with the given name to call');
    }
}
