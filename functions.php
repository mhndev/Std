<?php
namespace {
    !defined('POIROT_CORE_LOADED') and define('POIROT_CORE_LOADED', true);

    !defined('DS') and define('DS', DIRECTORY_SEPARATOR);

    # !! don't store this value, void mean everything nothing
    !defined('VOID') and define('VOID', "\0"/*uniqid('__not_set_value__')*/);
}

namespace Poirot\Std
{
    use Poirot\Std\Type\StdArray;
    use Poirot\Std\Type\StdString;
    use Poirot\Std\Type\StdTravers;

    trait SetterBuilderTrait
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
         * @return array Remained Options (if not throw exception)
         */
        function setupFromArray(array $setters, $throwException = false)
        {
            if (empty($setters))
                # nothing to do
                return $this;

            if (array_values($setters) == $setters)
                throw new \InvalidArgumentException(sprintf(
                    'Setters Array must be associative array. given: %s'
                    , var_export($setters, true)
                ));

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

            $remained = [];
            foreach($setters as $key => $val) {
                $setter = 'set' . sanitize_camelCase($key);
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
    }

    /**
     * Cast Given Value Into SplTypes
     * SplTypes Contains Some Utility For That Specific Type
     *
     * @param mixed $type
     *
     * @throws \UnexpectedValueException
     * @return StdString|StdArray|StdTravers|\SplType
     */
    function cast($type)
    {
        switch(1) {
            case is_string($type): $return = new StdString($type);
                break;
            case is_array($type) : $return = new StdArray($type);
                break;
            case ($type instanceof \Traversable) : $return = new StdTravers($type);
                break;

            default: throw new \UnexpectedValueException(sprintf(
                'Type (%s) is unexpected.', gettype($type)
            ));
        }

        return $return;
    }

    /**
     * Check Variable/Object Is String
     *
     * @param mixed $var
     *
     * @return bool
     */
    function is_string($var)
    {
        return (
            (!is_array($var))
            &&
            (
                (!is_object($var) && @settype($var, 'string') !== false)
                ||
                (is_object($var)  && method_exists($var, '__toString' ))
            )
        );
    }

    /**
     * Flatten Value
     *
     * @param mixed $value
     *
     * @return string
     */
    function flatten($value)
    {
        if ($value instanceof \Closure) {
            $closureReflection = new \ReflectionFunction($value);
            $value = sprintf(
                '(Closure at %s:%s)',
                $closureReflection->getFileName(),
                $closureReflection->getStartLine()
            );
        } elseif (is_object($value)) {
            $value = sprintf('%s:object(%s)', spl_object_hash($value), get_class($value));
        } elseif (is_resource($value)) {
            $value = sprintf('resource(%s-%s)', get_resource_type($value), $value);
        } elseif (is_array($value)) {
            foreach($value as $k => &$v)
                $v = flatten($v);

            $value = 'Array: ['.implode(', ', $value).']';
        } elseif (is_scalar($value)) {
            $value = sprintf('%s(%s)',gettype($value), $value);
        }

        return $value;
    }
}
