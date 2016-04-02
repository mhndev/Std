<?php
namespace Poirot\Std\Interfaces\Pact;

interface ipInvokableCallback extends ipInvokable
{
    /**
     * Set Callable Closure For __invoke
     *
     * - callable can have one optional argument. exp.func(arg)
     * - callable bind to this class as a closure, so with
     *   $this on function you can access methods/vars on this
     *   class
     *
     * @param callable $callable
     *
     * @return $this
     */
    function setCallable(callable $callable);

    /**
     * Get Callable
     *
     * @return callable
     */
    function getCallable();
}
