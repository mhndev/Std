<?php
namespace Poirot\Std\Interfaces\Pact;

interface ipBuilder
{
    /**
     * Setter Setup From Array
     *
     * @param array $setters        Associated Array
     *
     * @param bool  $throwException Throw Exception
     *
     * @throws \Exception
     * @return $this
     */
    function with(array $setters, $throwException = false);
}
