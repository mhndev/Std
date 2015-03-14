<?php
namespace Poirot\Core;

use Poirot\Core\Traits\OpenOptionsTrait;

class OpenOptions implements Interfaces\iPoirotOptions
{
    use OpenOptionsTrait;

    /**
     * Construct
     *
     * @param array|Interfaces\iPoirotOptions $options Options
     */
    function __construct($options = null)
    {
        if ($options !== null)
            $this->from($options);
    }
}
 