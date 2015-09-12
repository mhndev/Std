<?php
namespace Poirot\Core;

class BuilderSetter 
{
    use BuilderSetterTrait;

    /**
     * @var array List Setters By Priority
     * [
     *  'service_config',
     *  'listeners',
     *  // ...
     * ]
     *
     * application calls setter methods from top ...
     *
     */
    protected $__setup_array_priority = [];

    /**
     * Construct
     *
     * @param array $setter
     */
    function __construct(array $setter = null)
    {
        if ($setter !== null)
            $this->setupFromArray($setter);
    }
}
 