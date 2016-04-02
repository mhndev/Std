<?php
namespace Poirot\Std\Interfaces\Pact;

interface ipFactory
{
    /**
     * Factory With Valuable Parameter
     *
     * @param mixed $valuable
     *
     * @throws \Exception
     * @return mixed
     */
    static function of($valuable);
}
