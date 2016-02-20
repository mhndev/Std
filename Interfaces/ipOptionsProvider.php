<?php
namespace Poirot\Std\Interfaces;

use Poirot\Std\Struct\AbstractOptionsData;

/**
 * The Class That Implement This Interface
 * Provide Some Options Configuration
 *
 */
interface ipOptionsProvider
{
    /**
     * @return AbstractOptionsData
     */
    function inOptions();

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
     * @return AbstractOptionsData
     */
    static function newOptions($builder = null);
}
