<?php
namespace Poirot\Std\Type;

if (!class_exists('\SplType')) {
    require_once __DIR__.'/fixes/AbstractNSplType.php';
    class_alias('\Poirot\Std\Type\AbstractNSplType', '\SplType');
}

final class StdTravers extends \SplType
    implements \IteratorAggregate
{
    // TODO As of PHP 5.6 we can use math expressions in PHP constants
    // const __default = [];

    /** @var \Traversable */
    protected $value;

    /**
     * Creates a new value of some type
     *
     * @param \Traversable $initial_value
     * @param bool         $strict        If set to true then will throw UnexpectedValueException
     *                                    if value of other type will be assigned.
     *
     * @link http://php.net/manual/en/spltype.construct.php
     * // TODO As of PHP 5.6 we can use math expressions in PHP constants
     */
    function __construct($initial_value = null, $strict = true)
    {
        if ($initial_value instanceof \Traversable) {
            $this->value = $initial_value;
        } elseif ($strict)
            throw new \UnexpectedValueException(sprintf(
                'Type (%s) is unexpected.', gettype($initial_value)
            ));

        // with default value
    }


    // Implement Features:

    /**
     * Convert Iterator To An Array
     *
     * filter:
     * // return true mean not present to output array
     * bool function(&$val, &$key = null);
     *
     * @param \Closure|null $filter
     * @param bool          $recursive     Recursively convert values that can be iterated
     *
     * @return StdArray
     */
    function toArray(\Closure $filter = null, $recursive = true)
    {
        $arr = [];
        foreach($this->getIterator() as $key => $val) {
            $flag = false;
            if ($filter !== null)
                $flag = $filter($val, $key);

            if ($flag) continue;

            if ($recursive && $val instanceof \Traversable)
                ## deep convert
                $val = (new static($val))->toArray($filter);

            $arr[(string) $key] = $val;
        }

        return new StdArray($arr);
    }



    // Implement IteratorAggregate:

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        foreach($this->value as $key => $val)
            yield $key => $val;
    }
}
