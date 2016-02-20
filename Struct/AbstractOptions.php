<?php
namespace Poirot\Std\Struct;

use Poirot\Std;
use Poirot\Std\Struct\AbstractOptions\PropsObject;
use Traversable;

class AbstractOptions extends AbstractDataStruct
    implements Std\Interfaces\Struct\iOptionsData
{
    /** @var string|\DateTime @required yyyy-mm-ddThh:mm:ss (1983-08-13) */
    // protected $birthDate;
    /** @var string */
    // protected $mobile;
    /** @var int @required Gender 1=male|2=female */
    // protected $gender;
    /** @var string @required */
    // protected $passportNo;
    /** @var int @required description about field */
    // protected $planCode;

    protected $_t_options__ignored = [];

    /**
     * @var PropsObject Cached Props Once Call props()
     */
    protected $_cachedProps;

    /** @var \Closure Property keys normalizer */
    protected $__normalizer;

    /**
     * Do Set Data From
     * @param array|\Traversable $data
     */
    protected function doSetFrom($data)
    {
        foreach($data as $k => $v)
            $this->__set($k, $v);
    }

    /**
     * Ignore Some Method To Considered As Option Property
     *
     * ignore('isFulfilled', [$other...])
     *
     * @param $methodName
     * @param null $_
     *
     * @return $this
     */
    function ignore($methodName, $_ = null)
    {
        $ignoredMethods = func_get_args();
        foreach($ignoredMethods as $im)
            $this->_t_options__ignored[] = (string) $im;

        return $this;
    }

    /**
     * Get List Of Ignored Methods
     * @return array
     */
    protected function doWhichMethodIgnored()
    {
        static $init;
        if (is_null($init)) {
            ## Detect/Default Ignored
            ### Detect: by docblock
            $this->__ignoreFromDocBlock();

            ### Default: isFulfilled and isEmpty is public internal method and not option
            $x   = &$this->_t_options__ignored;
            $x[] = 'isFulfilled';
            $x[] = 'isEmpty';
            $x[] = 'getIterator';
        }

        return $this->_t_options__ignored;
    }

    /**
     * @ignore
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        /** @var PropsObject $p */
        foreach($this->__props() as $p) {
            $val = ($p->isReadable()) ? $this->__get($p->getKey()) : null;
            yield $p => $val;
        }
    }

    /**
     * Is Required Property Full Filled?
     * @ignore
     *
     * !! this method can override on classes that extend this
     *
     * @param null|string $property_key
     *
     * @return bool
     */
    function isFulfilled($property_key = null)
    {
        $fulFilled = true;

        if ($property_key !== null)
            $props = [(string)$property_key];
        else
            $props = $this->__props();

        /** @var PropsObject $propObject */
        foreach($props as $propObject) {
            if (!$propObject->isReadable()) continue;

            list($value, $expected) = $this->__extractValueAndExpectedMatchExpression($propObject->getKey());
            $fulFilled &= $this->__isValueMatchAsExpected($value, $expected);

            if (!$fulFilled)
                break; ## no more iteration
        }

        return (boolean) $fulFilled;
    }

    /**
     * NULL value for a property considered __isset false
     * @param mixed $key
     * @return bool
     */
    function has($key)
    {
        return $this->__isset($key);
    }

    /**
     * NULL value for a property considered __isset false
     * @param mixed $key
     * @return $this
     */
    function del($key)
    {
        $this->__unset($key);
        return $this;
    }

    /**
     * Empty from all values
     * @return $this
     */
    function emptyy()
    {
        /** @var PropsObject $k */
        foreach($this as $k => $v)
            $k->isReadable() && $this->del($k);

        return $this;
    }

    /**
     * Is Empty?
     * @return bool
     */
    function isEmpty()
    {
        $isEmpty = true;
        /** @var PropsObject $k */
        foreach($this as $k => $v) {
            if ($k->isReadable()) continue;
            $isEmpty = false;
            break;
        }

        return $isEmpty;
    }

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
        static $_c__sum;
        if ($_c__sum !== null)
            return $_c__sum;

        // ..

        /** @var PropsObject $p */
        $s = 0;
        foreach($this->__props() as $p) {
//            if (!$p->isReadable()) continue;
            $s++;
        }

        return $_c__sum = $s;
    }

    /**
     * - VOID values will unset attribute
     * @param string $key
     * @param mixed $value
     *
     * @throws \Exception
     * @return void
     */
    function __set($key, $value)
    {
        if ($setter = $this->_getSetterIfHas($key))
            $this->$setter($value);
        elseif ($this->__isset($key))
            throw new \Exception(sprintf(
                'The Property "%s" is readonly.'
                , $key
            ));
        else throw new \Exception(sprintf(
            'The Property (%s) not having any Public Setter Method Match on (%s).'
            , $key, get_class($this)
        ));
    }

    /**
     * !! Be Aware You Cant Use isset() inside getter methods itself
     *
     * @param string $key
     *
     * @throws \Exception
     * @return mixed
     */
    function __get($key)
    {
        $return = null;
        if ($getter = $this->_getGetterIfHas($key))
            $return = $this->$getter();
        elseif ($this->_isMethodExists('set' . $this->__normalize($key, 'internal')))
            throw new \Exception(sprintf(
                'The Property "%s" is writeonly.'
                , $key
            ));

        if ($return === null)
            throw new \Exception(sprintf(
                'The Property "%s" is not found.'
                , $key
            ));

        return $return;
    }

    /**
     * !! Be Aware You Cant Use isset() inside getter methods itself
     *
     * @param string $key
     * @return bool
     */
    function __isset($key)
    {
        $isset = false;
        try {
            $isset = ($this->__get($key) !== null);
        } catch(\Exception $e) { }

        return $isset;
    }

    /**
     * @param string $key
     * @return void
     */
    function __unset($key)
    {
        $this->__set($key, null);
    }


    // ...

    /**
     * Get Options Properties Information
     *
     */
    protected function __props()
    {
        props_st:
        if ($this->_cachedProps) {
            /** @var PropsObject $prop */
            foreach($this->_cachedProps as $prop)
                yield $prop;

            return;
        }

        $props   = [];

        $ref     = $this->_reflection();
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach($methods as $method) {
            foreach(['set', 'get', 'is'] as $prefix)
                if (strpos($method->getName(), $prefix) === 0) {
                    if (in_array($method->getName(), $this->doWhichMethodIgnored()))
                        ## it will use as internal option method
                        continue;

                    ## getAttributeName -> AttributeName
                    $propertyName = substr($method->getName(), strlen($prefix));
                    $propertyName = $this->__normalize($propertyName, 'external');

                    // mark readable/writable for property
                    (isset($props[$propertyName])) ?: $props[$propertyName] = new PropsObject($propertyName);
                    ($prefix == 'set')
                        ? $props[$propertyName]->setWritable()
                        : $props[$propertyName]->setReadable()
                    ;

                }
        }

        $this->_cachedProps = $props;
        goto props_st;
    }

    protected function _getGetterIfHas($key, $prefix = 'get')
    {
        $getter = $prefix . $this->__normalize($key, 'internal');
        if (! ( $result = $this->_isMethodExists($getter) ) && $prefix === 'get')
            return $this->_getGetterIfHas($key, 'is');

        return ($result) ? $getter : false;
    }

    protected function _getSetterIfHas($key)
    {
        $setter = 'set' . $this->__normalize($key, 'internal');
        return ($this->_isMethodExists($setter)) ? $setter : false;
    }

    /**
     * Property Key Normalizer
     * @param string $key
     * @param string $type internal|external
     * @return string
     */
    protected function __normalize($key, $type)
    {
        $type = strtolower($type);

        if ($type !== 'external' && $type !== 'internal')
            throw new \InvalidArgumentException;

        if (!isset($this->__normalizer['internal']))
            $this->__normalizer['internal'] = function($key) {
                return Std\sanitize_camelCase($key);
            };

        if (!isset($this->__normalizer['external']))
            $this->__normalizer['external'] = function($key) {
                return strtolower(Std\sanitize_under_score($key));
            };


        $return = $this->__normalizer[$type];
        $return = call_user_func($return, $key);
        return $return;
    }

    protected function __extractValueAndExpectedMatchExpression($property_key)
    {
        $ref = $this->_reflection();


        // ...

        $expectedValue = null;

        try{
            $currentValue  = $this->__get($property_key);
        } catch(\Exception $e) {
            ## not set so consider as void
            $currentValue = null;
        }

        // ...

        // detect required expected from Class DocBlock:
        /**
         * @property string sanitizedProperty @required description of property usage
         */
        $classDocComment = $ref->getDocComment();
        $regex = '/(@property\s*)(?P<expected>[\w\|]+\s*)('.$this->__normalize($property_key, 'internal').'+\s*)@required/';
        if ($classDocComment !== false && preg_match($regex, $classDocComment, $matches)) {
            $expectedValue = $matches['expected'];
            goto done;
        }

        // detect required expected from Method DocBlock:
        /**
         * @return string|null|object|\Stdclass|void
         */
        $methodName    = $this->_getGetterIfHas($property_key);
        if ($methodName) {
            $methodRefl    = $ref->getMethod($methodName);
            $methodComment = $methodRefl->getDocComment();

            $regex = '/(@required\s)(.*\s+|)+(@return\s(?P<expected>[\w\s\|]*))/';
            if ($methodComment !== false && preg_match($regex, $methodComment, $matches)) {
                $expectedValue = $matches['expected'];
                goto done;
            }
        }

        // detect required expected from Class Field DocBlock:
        /**
         * @var string|null|object|\Stdclass|void @required
         */
        try {
            $propRef     = $ref->getProperty($this->__normalize($property_key, 'internal'));
            $propComment = $propRef->getDocComment();
            $regex = '/(@var\s+)(?P<expected>[\w\s\|]*)(@required)/';
            if ($propComment !== false && preg_match($regex, $propComment, $matches)) {
                $expectedValue = $matches['expected'];
                goto done;
            }
        } catch(\Exception $e) {}

        done:
        return [$currentValue, $expectedValue];
    }

    /**
     * Match a value against expected docblock comment
     * @param mixed  $value
     * @param string $expectedString
     * @return bool
     */
    protected function __isValueMatchAsExpected($value, $expectedString)
    {
        $match = false;
        if ($expectedString == null)
            ## undefined expected values must not be NULL
            ## except when it write down on docblock "@return void"
            return $value !== null;

        $valueType = strtolower(gettype($value));

        /**
         * @return string|null|object|\Stdclass|void
         */
        $expectedString = explode('|', $expectedString);
        foreach($expectedString as $ext) {
            $ext = strtolower(trim($ext));
            if ($ext == '') continue;

            if ($value === VOID && $ext == 'void')
                $match = true;
            elseif ($valueType === $ext && $value != null)
                $match = true;
            elseif ($valueType === 'object') {
                if (is_a($value, $ext))
                    $match = true;
            }

            if ($match) break;
        }

        return $match;
    }

    /**
     * Ignore Methods that Commented as DocBlocks
     *
     */
    protected function __ignoreFromDocBlock()
    {
        $ref = $this->_reflection();

        // ignored methods from Class DocComment:
        $classDocComment = $ref->getDocComment();
        if (preg_match_all('/.*[\n]?/', $classDocComment, $lines)) {
            $lines = $lines[0];
            $regex = '/.+(@method).+((?P<method_name>\b\w+)\(.*\))\s@ignore\s/';
            foreach($lines as $line) {
                if (preg_match($regex, $line, $matches))
                    $this->ignore($matches['method_name']);
            }
        }

        // ignored methods from Method DocBlock
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach($methods as $m) {
            $mc = $m->getDocComment();
            if (preg_match('/@ignore\s/', $mc, $matches))
                $this->ignore($m->getName());
        }
    }

    /**
     * Is Setter Property Method?
     *
     * @param string $method Method Name
     *
     * @return bool
     */
    protected function _isMethodExists($method)
    {
        $return = method_exists($this, $method);
        if ($return) {
            ## it must be exists also be public accessible
            $ref    = $this->_reflection();
            $ref    = $ref->getMethod($method);
            $return = $return && $ref->isPublic();
        }

        return $return && !in_array($method, $this->doWhichMethodIgnored());
    }

    /**
     * @return \ReflectionClass
     */
    protected function _reflection()
    {
        static $static;
        if (is_null($static))
            $static = new \ReflectionClass($this);

        return $static;
    }
}
