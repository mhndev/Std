<?php
namespace Poirot\Std\Interfaces;

use Poirot\Std\Struct\AbstractOptions;

/**
 * The Class That Implement This Interface
 * Provide Some Options Configuration
 *
 */
interface ipOptionsProvider
{
    /**
     * @return AbstractOptions
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
     * @return AbstractOptions
     */
    static function newOptions($builder = null);
}