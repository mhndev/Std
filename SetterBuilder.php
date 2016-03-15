<?php
namespace Poirot\Std;

use Poirot\Std\Traits\SetterBuilderTrait;

class SetterBuilder
{
    use SetterBuilderTrait;

    /**
     * Construct
     *
     * @param array $setter
     */
    function __construct(array $setter = null)
    {
        if ($setter !== null)
            $this->build($setter);
    }
}
