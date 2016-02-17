<?php
namespace Poirot\Std;

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
     * @param array $realm
     */
    function __construct(array $realm = null)
    {
        if ($realm !== null)
            $this->setupFromArray($realm);
    }
}
 