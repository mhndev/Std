<?php
namespace Poirot\Std\Struct\Traits;

use Poirot\Std;
use Poirot\Std\Interfaces\Struct\iOptionDataStruct;
use Poirot\Std\Interfaces\Struct\iDataStruct;
use Poirot\Std\Struct\AbstractOptions\PropsObject;

/**
 * ignore:
 * @method $this setPropKeyNormalizer(callable $callable) @ignore // ignore this method as option
 *
 * required:
 * @property string sanitizedProperty @required description of property usage
 */
trait OptionsTrait
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

    /** @var \Closure Property keys normalizer */
    protected $normalizer;

    /**
     * @var PropsObject Cached Props Once Call props()
     */
    protected $_cachedProps;


    /**
     * Set Options
     *
     * - Object instance of this call fromSimilar
     *
     * @param array|iOptionDataStruct|iDataStruct $options
     *
     * @return $this
     */
    function from($options)
    {
        if ($options instanceof $this)
            return $this->fromSimilar($options);

        if ($options instanceof iDataStruct)
            $options = $options->toArray();

        if (is_array($options))
            $this->fromArray($options);
        else
            throw new \InvalidArgumentException(sprintf(
                'Can`t create class "%s" with option type(%s).'
                , get_class($this), Std\flatten($options)
            ));

        return $this;
    }

    /**
     * Set Options From Array
     *
     * @param array $options Options Array
     *
     * @throws \Exception
     * @return $this
     */
    function fromArray(array $options)
    {
        if (!empty($options) && array_values($options) === $options)
            throw new \InvalidArgumentException('Options Array must be associative array.');

        foreach($options as $key => $val)
            $this->__set($key, $val);

        return $this;
    }

    /**
     * Set Options From Same Option Object
     *
     * note: it will take an option object instance of $this
     *       OpenOptions only take OpenOptions as argument
     *
     * - also you can check for private and write_only
     *   methods inside Options Object to get fully coincident copy
     *   of Options Class Object
     *
     * @param iOptionDataStruct $context Options Object
     *
     * @throws \Exception
     * @return $this
     */
    function fromSimilar(/*iOptionImplement*/ $context)
    {
        if (!$context instanceof $this)
            // only get same option object
            throw new \Exception(sprintf(
                'Given Options Is Not Same As Provided Class Options. you given (%s).'
                , Std\flatten($context)
            ));

        $this->fromArray($context->toArray());
        return $this;

        // call your inherit options actions:
        // maybe you want access protected methods or properties
        // ...
    }

    /**
     * Get Options Properties Information
     *
     * @return PropsObject
     */
    function props()
    {
        if ($this->_cachedProps)
            return $this->_cachedProps;

        $ref     = $this->_reflection();
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        $props   = [];
        foreach($methods as $i => $method) {
            foreach(['set', 'get', 'is'] as $prefix)
                if (strpos($method->getName(), $prefix) === 0) {
                    if (in_array($method->getName(), $this->doWhichMethodIgnored()))
                        ## it will use as internal option method
                        continue;

                    ## set --> props['writable']
                    $props[($prefix == 'set') ? 'writable' : 'readable'][] = $this->__normalize(
                        ## getAttributeName -> AttributeName
                        substr($method->getName(), strlen($prefix))
                        , 'external'
                    );
                }
        }

        return $this->_cachedProps = new PropsObject($props);
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
        }

        return $this->_t_options__ignored;
    }

    /**
     * Get Properties as array
     *
     * @param \Closure|null $normalizer Normalize Keys string func(string $key)
     *
     * @throws \Exception
     * @return array
     */
    function toArray(\Closure $normalizer = null)
    {
        $rArray = [];
        foreach($this->props()->readable as $p) {
            if (!$this->__isset($p))
                continue;

            $val = $this->__get($p);
            ($normalizer === null) ?: $p = $normalizer($p);
            $rArray[$p] = $val;
        }

        return $rArray;
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
            $props = $this->props()->readable;

        foreach($props as $propName) {
            list($value, $expected) = $this->__extractValueAndExpectedMatchExpression($propName);
            $fulFilled &= $this->__isValueMatchAsExpected($value, $expected);

            if (!$fulFilled)
                break; ## no more iteration
        }

        return (boolean) $fulFilled;
    }

    /**
     * Clear All Property Data
     *
     * - value of each property is VOID
     *
     * @return void
     */
    function clear()
    {
        foreach($this->props()->readable as $p)
            $this->__unset($p);
    }

    /**
     * Has no property defined and is clear?
     *
     * @ignore
     * @return bool
     */
    function isEmpty()
    {
        $props = $this->toArray();
        return empty($props);
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
        $return = VOID;
        if ($getter = $this->_getGetterIfHas($key))
            $return = $this->$getter();
        elseif ($this->_isMethodExists('set' . $this->__normalize($key, 'internal')))
            throw new \Exception(sprintf(
                'The Property "%s" is writeonly.'
                , $key
            ));

        if ($return === VOID)
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
            $isset = ($this->__get($key) !== VOID);
        } catch(\Exception $e) { }

        return $isset;
    }

    /**
     * @param string $key
     * @return void
     */
    function __unset($key)
    {
        $this->__set($key, VOID);
    }


    // ...

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

        if (!isset($this->normalizer['internal']))
            $this->normalizer['internal'] = function($key) {
                return Std\sanitize_camelCase($key);
            };

        if (!isset($this->normalizer['external']))
            $this->normalizer['external'] = function($key) {
                return strtolower(Std\sanitize_under_score($key));
            };


        $return = $this->normalizer[$type];
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
            $currentValue = VOID;
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
            ## undefined expected values must not be VOID
            ## except when it write down on docblock "@return void"
            return $value !== VOID;

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
            elseif ($valueType === $ext && $value != VOID)
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
