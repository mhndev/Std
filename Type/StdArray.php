<?php
namespace Poirot\Std\Type;

use Traversable;

if (!class_exists('\SplType')) {
    require __DIR__.'/fixes/AbstractNSplType.php';
    class_alias('\Poirot\Std\Type\AbstractNSplType', '\SplType');
}

/*

// lowercase exact item
$name = &$stdArr->select('/1/Passenger/name');
$name = strtolower($name);

// lowercase whole passenger names
// it will return array of matched query
$credentials = $stdArr->select('/* /Passenger/name');
foreach($credentials as &$v)
    $v = strtolower($v);

$allNamesWithPassenger = &$stdArr->select('//Passenger//name');

*/

final class StdArray extends \SplType
    implements \ArrayAccess
    , \Countable
    , \IteratorAggregate
{
    // TODO As of PHP 5.6 we can use math expressions in PHP constants
    // const __default = [];

    public $value = [];

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
        if (is_array($initial_value))
            $this->value = $initial_value;
        elseif ($strict)
            throw new \UnexpectedValueException(sprintf(
                'Type (%s) is unexpected.', gettype($initial_value)
            ));

        // with default value
    }


    // Implement Features:

    /**
     * Select Bunch Of Items Regard To Given Query
     *
     * !! for reference return must call with reference
     *    &$stdArr->select('/1/Passenger/Credentials');
     *
     * $result->select('/* /Passengers');
     * mean from root-any key presentation - that contains Passenger
     *
     * $result->select('/insurance|hotels/Passengers');
     * mean from root-insurance|hotels-that contains Passenger
     *
     * @param string $query
     *
     * @return &mixed
     */
    function &select($query)
    {
        $_f__select = function & ($query, &$stackArr) use (&$_f__select)
        {
            $queryOrig = $query;
            if (strpos($query, '/') === 0)
                ## ignore first slash from instructions
                ## it cause an empty unwanted item on command stacks(exploded instructions)
                ## (withespace instruction has meaningful command "//item_on_any_depth")
                $query = substr($query, 1);

            if (!is_array($stackArr) && $query !== '') {
                // notice: only variable by reference must return
                $z = null;
                return $x = &$z;
            }


            if ($query === '')
                return $stackArr;

            $instructions = explode('/', $query);
            $ins          = array_shift($instructions);
            $remainQuery  = implode('/', $instructions);

            ## match find(//):
            if ($ins === '') {
                ### looking for any array elements to match query
                $return = [];
                foreach($stackArr as &$v) {
                    $r = &$_f__select($remainQuery, $v);
                    if ($r !== null)
                        $return[] = &$r;

                    if (is_array($v)) {
                        #### continue with deeper data
                        $r = &$_f__select($queryOrig, $v);
                        if ($r !== null) {
                            $return = array_merge($return, $r);
                        }
                    }
                }

                if (empty($return)) {
                    // notice: only variable by reference must return
                    $z = null;
                    return $x = &$z;
                }

                return $return;
            }

            ## match wildcard:
            if ($ins === '*') {
                $return = [];
                foreach($stackArr as &$v)
                    $return[] = &$_f__select($remainQuery, $v);

                return $return;
            }

            ## match data item against current query instruct:
            if (array_key_exists($ins, $stackArr))
                ### looking for exact match of an item:
                ### /*/[query/to/match/item]
                return $_f__select($remainQuery, $stackArr[$ins]);
            else {
                ## nothing match query
                // notice: only variable by reference must return
                $z = null;
                return $x = &$z;
            }
        };

        return $_f__select($query, $this->value);
    }

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
        foreach($this->value as $key => $val) {
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
        $a = $this->value;

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
        $a = $this->value;

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
        $data = $this->value;
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
        return isset($this->value[$offset]);
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
        return $this->value[$offset];
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
        $this->value[$offset] = $value;
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
        unset($this->value[$offset]);
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
        return count($this->value);
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
        return (new \ArrayObject($this->value))->getIterator();
    }
}
