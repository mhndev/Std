<?php
namespace Poirot\Std\Interfaces\Pact;

/**
 * Factory Classes Must Start With Factory prefix
 * exp. FactoryEnvironment
 */
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
