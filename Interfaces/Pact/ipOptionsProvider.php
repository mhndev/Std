<?php
namespace Poirot\Std\Interfaces\Pact;

use Poirot\Std\Interfaces\Struct\iDataOptions;

/**
 * The Class That Implement This Interface
 * Provide Some Options Configuration
 *
 */
interface ipOptionsProvider
{
    /**
     * @return iDataOptions
     */
    function optsData();

    /**
     * Get An Bare Options Instance
     *
     * ! it used on easy access to options instance
     *   before constructing class
     *   [php]
     *      $opt = Filesystem::optionsIns();
     *      $opt->setSomeOption('value');
     *
     *      $class = new Filesystem($opt);
     *   [/php]
     *
     * @param null|mixed $builder Builder Options as Constructor
     *
     * @return iDataOptions
     */
    static function newOptsData($builder = null);
}
