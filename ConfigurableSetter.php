<?php
namespace Poirot\Std;

use Poirot\Std\Traits\ConfigurableSetterTrait;

class ConfigurableSetter
{
    use ConfigurableSetterTrait;

    /**
     * Construct
     *
     * @param array $setter
     */
    function __construct(array $setter = null)
    {
        if ($setter !== null)
            $this->with($setter);
    }
}
