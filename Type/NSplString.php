<?php
namespace Poirot\Std\Type;

/**
 * This class used as alias of \SplString if it not exists
 * @see StdString
 */
class NSplString extends AbstractNSplType
{
    const __default = '';

    /** @var string */
    protected $value;

    /**
     * Creates a new value of some type
     *
     * @param mixed $initial_value
     * @param bool $strict  If set to true then will throw UnexpectedValueException if value of other type will be assigned. True by default
     * @link http://php.net/manual/en/spltype.construct.php
     */
    function __construct ($initial_value = self::__default, $strict = true )
    {
        if (is_object($initial_value)) {
            if (\Poirot\Std\is_string($initial_value))
                $initial_value = (string) $initial_value;
        }

        if (is_string($initial_value)) {
            $this->value = $initial_value;
        } elseif ($strict)
            throw new \UnexpectedValueException(sprintf(
                'Type (%s) is unexpected.', gettype($initial_value)
            ));

        // with default value
    }


    // ...

    function __toString()
    {
        return $this->value;
    }
}
