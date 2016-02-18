<?php
namespace Poirot\Std\Interfaces;

use Poirot\Std\Interfaces\Struct\iMeanDataStruct;

interface ipMetaProvider
{
    /**
     * @return iMeanDataStruct
     */
    function meta();
}
