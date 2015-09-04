<?php
namespace Poirot\Core\Interfaces;

interface iBuilderSetter 
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