<?php
namespace Poirot\Core;

!defined('POIROT_CORE_LOADED') and include_once 'Core.php';

use Poirot\Core;
use Poirot\Core\Interfaces\iPoirotEntity;

class Entity implements iPoirotEntity
{
    use Core\Traits\EntityTrait;
}
