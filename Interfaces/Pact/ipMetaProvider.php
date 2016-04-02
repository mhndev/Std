<?php
namespace Poirot\Std\Interfaces\Pact;

use Poirot\Std\Interfaces\Struct\iMeanData;

interface ipMetaProvider
{
    /**
     * @return iMeanData
     */
    function meta();
}
