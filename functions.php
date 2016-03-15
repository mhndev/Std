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
            case isString($type): $return = new StdString($type);
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
    function isString($var)
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
