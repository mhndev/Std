<?php
namespace Poirot\Std\Struct;

!defined('POIROT_CORE_LOADED') and include_once __DIR__.'/../functions.php';

use Poirot\Std;
use Poirot\Std\Interfaces\ipEntity;

class Entity implements ipEntity
{
    use Std\Struct\Traits\EntityTrait;

    /**
     * Construct
     *
     * @param array|ipEntity $props Properties
     *
     * @throws \Exception
     */
    function __construct($props = null)
    {
        if ($props)
            $this->from($props);
    }
}
