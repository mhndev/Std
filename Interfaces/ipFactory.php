<?php
namespace Poirot\Std\Interfaces;

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
