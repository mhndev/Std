<?php
namespace Poirot\Std\Type;

use Traversable;

if (!class_exists('\SplType'))
    class_alias('\Poirot\Std\Type\AbstractNSplType', '\SplType');

final class StdArray extends \SplType
    implements \ArrayAccess
    , \Countable
    , \IteratorAggregate
{
    // TODO As of PHP 5.6 we can use math expressions in PHP constants
    // const __default = [];


    /**
     * Creates a new value of some type
     *
     * @param array $initial_value
     * @param bool  $strict  If set to true then will throw UnexpectedValueException
     *                       if value of other type will be assigned.
     *
     * @link http://php.net/manual/en/spltype.construct.php
     * // TODO As of PHP 5.6 we can use math expressions in PHP constants
     */
    function __construct($initial_value = []/*self::__default*/, $strict = true)
    {
        if (is_array($initial_value)) {
            foreach($initial_value as $key => $val)
                // so it can be easily convert into array by type cast (array)
                $this->{$key} = $val;
        } elseif ($strict)
            throw new \UnexpectedValueException(sprintf(
                'Type (%s) is unexpected.', gettype($initial_value)
            ));

        // with default value
    }


    // Implement Features:

    /**
     * Walk an Array And Filter Or Manipulate Items Of Array
     *
     * filter:
     * // return true mean not present to output array
     * bool function(&$val, &$key = null);
     *
     * @param \Closure $filter
     * @param bool     $recursive  Recursively convert values that can be iterated
     *
     * @return StdArray
     */
    function walk(\Closure $filter, $recursive = true)
    {
        $arr = [];
        foreach((array) $this as $key => $val) {
            $flag = false;
            if ($filter !== null)
                $flag = $filter($val, $key);

            if ($flag) continue;

            if ($recursive && is_array($val))
                ## recursively walk
                $val = (new static($val))->walk($filter);

            $arr[(string) $key] = $val;
        }

        return new StdArray($arr);
    }

    /**
     * Merge two arrays together.
     *
     * If an integer key exists in both arrays, the value from the second array
     * will be appended the the first array. If both values are arrays, they
     * are merged together, else the value of the second array overwrites the
     * one of the first array.
     *
     * @param  array|StdArray $b
     * @return array
     */
    function merge($b)
    {
        $b = (array) $b;
        $a = (array) $this;

        foreach ($b as $key => $value)
            if (array_key_exists($key, $a)) {
                if (is_int($key)) {
                    if (!in_array($value, $a))
                        $a[] = $value;
                }
                elseif (is_array($value) && is_array($a[$key]))
                    $a[$key] = \Poirot\Std\array_merge($a[$key], $value);
                else
                    $a[$key] = $value;
            } else
                $a[$key] = $value;

        return new static($a);
    }

    /**
     * Merge two arrays together, reserve previous values
     *
     * @param  array|StdArray $b
     * @return array
     */
    function mergeRecursive($b)
    {
        $b = (array) $b;
        $a = (array) $this;

        foreach ($b as $key => $value)
            if (array_key_exists($key, $a)) {
                if (is_int($key)) {
                    if (!in_array($value, $a))
                        $a[] = $value;
                }
                elseif (is_array($value) && is_array($a[$key]))
                    $a[$key] = \Poirot\Std\array_merge_recursive($a[$key], $value);
                else {
                    $cv = $a[$key];
                    $a[$key] = [];
                    $pa = &$a[$key];
                    array_push($pa, $cv);
                    array_push($pa, $value);
                }
            } else
                $a[$key] = $value;

        return new static($a);
    }

    /**
     * Is This An Associative Array?
     *
     * @return bool
     */
    function isAssoc()
    {
        $data = (array) $this;
        return (array_values($data) !== $data);
    }

    // Implement ArrayAccess:

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function &offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }


    // Implement Countable:

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count((array)$this);
    }


    // Implement IteratorAggregate:

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return (new \ArrayObject((array)$this))->getIterator();
    }
}
