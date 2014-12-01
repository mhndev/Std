<?php
namespace Poirot\Core
{
    trait SetterSetup
    {
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

            foreach($setters as $key => $val) {
                $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                if (method_exists($this, $setter)) {
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
}