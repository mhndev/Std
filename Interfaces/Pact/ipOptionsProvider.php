<?php
namespace Poirot\Std\Interfaces\Pact;

use Poirot\Std\Interfaces\Struct\iOptionsData;

/**
 * The Class That Implement This Interface
 * Provide Some Options Configuration
 *
 */
interface ipOptionsProvider
{
    /**
     * @return iOptionsData
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
     * @return iOptionsData
     */
    static function newOptsData($builder = null);
}
