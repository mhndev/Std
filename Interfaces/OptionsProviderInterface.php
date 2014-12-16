<?php
namespace Poirot\Core\Interfaces;

use Poirot\Core\AbstractOptions;

/**
 * The Class That Implement This Interface
 * Provide Some Options Configuration
 *
 */
interface OptionsProviderInterface 
{
    /**
     * @return AbstractOptions
     */
    function options();
}
