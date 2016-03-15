<?php
namespace Poirot\Std\Traits;

trait SetterBuilderTrait
{
    /**
     * [
     *  'service_config',
     *  'listeners',
     *  // ...
     * ]
     */
    protected $_t__props_priorities = [];

    /**
     * Setter Setup From Array
     *
     * @param array $setters        Associated Array
     *
     * @param bool  $throwException Throw Exception
     *
     * @throws \Exception
     * @return array Remained Options (if not throw exception)
     */
    function build(array $setters, $throwException = false)
    {
        if (empty($setters))
            # nothing to do
            return $this;

        if (array_values($setters) == $setters)
            throw new \InvalidArgumentException(sprintf(
                'Setters Array must be associative array. given: %s'
                , var_export($setters, true)
            ));

        if (isset($this->_t__props_priorities)
            && is_array($this->_t__props_priorities)
        ) {
            $sortQuee = $this->_t__props_priorities;
            uksort($setters, function($a, $b) use ($sortQuee) {
                // sort array to reach setter priorities
                $ai = array_search($a, $sortQuee);
                $ai = ($ai !== false) ? $ai : 1000;

                $bi = array_search($b, $sortQuee);
                $bi = ($bi !== false) ? $bi : 1000;

                return $ai < $bi ? -1 : ($ai == $bi) ? 0 : 1;
            });
        }

        $remained = [];
        foreach($setters as $key => $val) {
            $setter = 'set' . \Poirot\Std\cast($key)->camelCase();
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
            else
                $remained[] = $key;
        }

        return $remained;
    }

    /**
     * List Setters By Priority
     *
     * [
     *  'service_config',
     *  'listeners',
     *  // ...
     * ]
     *
     * application calls setter methods from top ...
     *
     * @param array $propPriorities
     */
    protected function putBuildPriority(array $propPriorities)
    {
        $this->_t__props_priorities = $propPriorities;
    }
}
