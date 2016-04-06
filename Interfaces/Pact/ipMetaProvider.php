<?php
namespace Poirot\Std\Interfaces\Pact;

use Poirot\Std\Interfaces\Struct\iDataMean;

interface ipMetaProvider
{
    /**
     * @return iDataMean
     */
    function meta();
}
