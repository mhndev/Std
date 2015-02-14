<?php
namespace {
    !defined('POIROT_CORE_LOADED') and define('POIROT_CORE_LOADED', true);
}

namespace Poirot\Core
{
    trait BuilderSetterTrait
    {
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
        # protected $__setup_array_priority = array();

        /**
         * Setter Setup From Array
         *
         * @param array $setters        Associated Array
         *
         * @param bool  $throwException Throw Exception
         *
         * @throws \Exception
         * @return $this
         */
        function setupFromArray(array $setters, $throwException = false)
        {
            if (array_values($setters) == $setters)
                throw new \InvalidArgumentException('Setters Array must be associative array.');

            if (isset($this->__setup_array_priority)
                && is_array($this->__setup_array_priority)
            ) {
                $sortQuee = $this->__setup_array_priority;
                uksort($setters, function($a, $b) use ($sortQuee) {
                    // sort array to reach setter priorities
                    $ai = array_search($a, $sortQuee);
                    $ai = ($ai !== false) ? $ai : 1000;

                    $bi = array_search($b, $sortQuee);
                    $bi = ($bi !== false) ? $bi : 1000;

                    return $ai < $bi ? -1 : ($ai == $bi) ? 0 : 1;
                });
            }

            foreach($setters as $key => $val) {
                $setter = 'set' . sanitize_camelcase($key);
                if (method_exists($this, $setter)) {
                    // check for methods
                    $this->{$setter}($val);
                } elseif($throwException) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'The option "%s" does not have a matching "%s" setter method',
                            $key, $setter
                        )
                    );
                }
            }

            return $this;
        }
    }

    /**
     * Merge two arrays together.
     *
     * If an integer key exists in both arrays, the value from the second array
     * will be appended the the first array. If both values are arrays, they
     * are merged together, else the value of the second array overwrites the
     * one of the first array.
     *
     * @param  array $a
     * @param  array $b
     * @return array
     */
    function array_merge(array $a, array $b)
    {
        foreach ($b as $key => $value)
            if (array_key_exists($key, $a)) {
                if (is_int($key)) {
                    if (!in_array($value, $a))
                        $a[] = $value;
                }
                elseif (is_array($value) && is_array($a[$key]))
                    $a[$key] = array_merge($a[$key], $value);
                else
                    $a[$key] = $value;
            } else
                $a[$key] = $value;

        return $a;
    }

    /**
     * Sanitize Underscore To Camelcase
     *
     * @param string $key Key
     *
     * @return string
     */
    function sanitize_camelcase($key)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
    }

    /**
     * Sanitize CamelCase To under_score
     *
     * @param string $key Key
     *
     * @return string
     */
    function sanitize_underscore($key)
    {
        $pattern     = array('#(?<=(?:[A-Z]))([A-Z]+)([A-Z][A-z])#', '#(?<=(?:[a-z0-9]))([A-Z])#');
        $replacement = array('\1_\2', '_\1');

        return preg_replace($pattern, $replacement, $key);
    }
}
