<?php
namespace Poirot\Std\Interfaces;

interface iPoirotOptions extends iOptionImplement
{
    /**
     * Construct
     *
     * @param array|iOptionImplement|mixed $options Options
     */
    function __construct($options = null);
}
