<?php
namespace Poirot\Std\Interfaces\Pact;

interface ipConfigurable
{
    /**
     * Build Object With Provided Options
     *
     * @param array $options        Associated Array
     * @param bool  $throwException Throw Exception On Wrong Option
     *
     * @throws \Exception
     * @return $this
     */
    function with(array $options, $throwException = false);
}
