<?php
namespace Poirot\Std\Type;

require_once __DIR__.'/AbstractNSplType.php';

class NSplEnum extends AbstractNSplType
{
    /* Constants */
    const __default = null ; // self::CONSTANT_VAL

    // const CONSTANT_VAL = 1;
    // ...

    protected $value = self::__default;

    protected $_c__consts;

    /**
     * Creates a new value of some type
     *
     * @param mixed $initial_value
     * @param bool $strict  If set to true then will throw UnexpectedValueException if value of other type will be assigned. True by default
     * @link http://php.net/manual/en/spltype.construct.php
     */
    function __construct ($initial_value = self::__default, $strict = true )
    {
        if (in_array($initial_value, $this->getConstList(true))) {
            $this->value = $initial_value;
        } elseif ($strict)
            throw new \UnexpectedValueException(sprintf(
                'Type (%s) is unexpected.', gettype($initial_value)
            ));

        // with default value
    }

    /**
     * Get Constant Lists
     * @param bool $include_default
     * @return array
     */
    function getConstList($include_default = false)
    {
        if (isset($this->_c__consts))
            return $this->_c__consts;

        $reflection = new \ReflectionClass($this);
        $consts     = $reflection->getConstants();
        if ((bool)$include_default === false)
            unset($consts['__default']);

        return $this->_c__consts = $consts;
    }
}
