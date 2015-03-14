<?php
namespace Poirot\Core\Interfaces;

interface iPoirotOptions extends iOptionImplement
{
    /**
     * Construct
     *
     * @param array|iOptionImplement $options Options
     */
    function __construct($options = null);
}
