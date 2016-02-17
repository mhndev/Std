<?php
namespace poirot\std\Poirot\Std\Interfaces;

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
    static function with($valuable);
}
