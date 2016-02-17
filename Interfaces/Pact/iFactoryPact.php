<?php
namespace poirot\std\Poirot\Std\Interfaces\Pact;

interface iFactoryPact
{
    /**
     * Factory With Valuable Parameter
     *
     * @param mixed $valuable
     *
     * @throws \Exception
     * @return mixed
     */
    static function with($valuable);
}
