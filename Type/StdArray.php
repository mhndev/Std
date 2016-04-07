<?php
namespace Poirot\Std\Type;

if (!class_exists('\SplString')) {
    require_once __DIR__.'/fixes/NSplString.php';
    class_alias('\Poirot\Std\Type\NSplString', '\SplString');
}

final class StdString extends \SplString
{
    /**
     * Sanitize Underscore To Camelcase
     *
     * @return string
     */
    function camelCase()
    {
        $Pascal = lcfirst((string)$this->PascalCase());
        return new static($Pascal);
    }

    /**
     * Sanitize Underscore To Camelcase
     *
     * @return string
     */
    function PascalCase()
    {
        $key = (string) $this;
        return new static(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
    }

    /**
     * Sanitize CamelCase To under_score
     *
     * @return string
     */
    function under_score()
    {
        $key = (string) $this;

        $pattern     = ['#(?<=(?:[A-Z]))([A-Z]+)([A-Z][A-z])#', '#(?<=(?:[a-z0-9]))([A-Z])#'];
        $replacement = ['\1_\2', '_\1'];

        return new static(strtolower(preg_replace($pattern, $replacement, $key)));
    }
}
