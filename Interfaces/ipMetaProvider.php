<?php
namespace Poirot\Std\Interfaces;

use Poirot\Std\Interfaces\Struct\iMeanStruct;

interface ipMetaProvider
{
    /**
     * @return iMeanStruct
     */
    function meta();
}
