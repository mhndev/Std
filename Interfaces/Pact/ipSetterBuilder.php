<?php
namespace Poirot\Std\Interfaces\Pact;

interface ipSetterBuilder
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
    function setupFromArray(array $setters, $throwException = false);
}
