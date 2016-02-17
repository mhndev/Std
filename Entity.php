<?php
namespace Poirot\Std;

!defined('POIROT_CORE_LOADED') and include_once 'Core.php';

use Poirot\Std;
use Poirot\Std\Interfaces\iPoirotEntity;

class Entity implements iPoirotEntity
{
    use Std\Traits\EntityTrait;
}
