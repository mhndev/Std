<?php
namespace Poirot\Core;

use Poirot\Core;
use Poirot\Core\AbstractOptions\PropsObject;

/**
 * Here is a simple optionsClass example:
 *
 * ~~~
 * class Options extend AbstractOptions {
 *
 *  // >>> Plug(Full) Properties >>>>>
 *
 *  protected $fname;
 *
 *  protected $prefix = '';
 *
 *  // Property Setter/Getter Methods must be Public
 *
 *  public function setFullName($fname)
 *  {
 *      $this->fname = $fname;
 *  }
 *
 *  public function getFullName()
 *  {
 *      return $this->prefix.$this->fname;
 *  }
 *
 *  // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
 *
 *  // >>> ReadOnly Access >>>>>
 *
 *  public function getClassName()
 *  {
 *      return get_class($this);
 *  }
 *
 *  // <<<<<<
 *
 * // >>> WriteOnly Access >>>>>
 *
 *  public function setPrefix($prefix)
 *  {
 *      $this->prefix = $prefix
 *  }
 *
 *  // <<<<<<
 * }
 * ~~~
 *
 * How to use it:
 *
 * ~~~
 * $opt = new Options(['prefix' => 'Eng.', 'full_name' => 'Payam Naderi']);
 * $opt->setPrefix('Eng.'); // same as above
 * foreach($opt->props()->readable as $key) // get all readable props
 *  if (!empty($opt->$key))
 *      echo($opt->$key); // get key value
 *
 * echo $opt->getClassName();
 * ~~~
 *
 */
abstract class AbstractOptions implements Interfaces\iMagicalFields
{
    /**
     * @var PropsObject Cached Props Once Call props()
     */
    protected $_cachedProps;

    /**
     * Construct
     *
     * @param array|AbstractOptions $options Options
     */
    function __construct($options = null)
    {
        if ($options !== null)
            $this->from($options);
    }

    /**
     * Set Options
     *
     * @param array|AbstractOptions $options
     *
     * @return $this
     */
    public function from($options)
    {
        if (is_array($options))
            $this->fromArray($options);
        elseif ($options instanceof AbstractOptions)
            $this->fromOption($options);

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
        if (array_values($options) == $options)
            throw new \InvalidArgumentException('Options Array must be associative array.');

        foreach($options as $key => $val)
            $this->__set($key, $val);

        return $this;
    }

    /**
     * Set Options From Same Option Object
     *
     * - check for private and write_only methods
     *   to fully options copied to source option
     *   class object
     *
     * @param AbstractOptions $options Options Object
     *
     * @throws \Exception
     * @return $this
     */
    function fromOption(/*AbstractOptions*/ $options) // php is a donkey, why strict_error
    {
        if (!$options instanceof $this)
            // only get same option object
            throw new \Exception(sprintf(
                'Given Options Is Not Same As Provided Class Options. you given "%s".'
                , get_class($options)
            ));

        foreach($options->props()->writable as $key)
            if (isset($options->{$key}))
                $this->__set($key, $options->{$key});

        // call your inherit options actions:
        // maybe you want access protected methods or properties
        // ...

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @throws \Exception
     * @return void
     */
    public function __set($key, $value)
    {
        $setter = 'set' . sanitize_camelcase($key);
        if ($this->isMethodExists($setter))
            $this->$setter($value);
        elseif ($this->__isset($key))
            throw new \Exception(sprintf(
                'The Property "%s" is readonly.'
                , $key
            ));
        else throw new \Exception(sprintf(
            'The Property "%s" not having any Public Setter Method Match.'
            , $key
        ));
    }

    /**
     * @param string $key
     *
     * @throws \Exception
     * @return mixed
     */
    public function __get($key)
    {
        $getter = 'get' . sanitize_camelcase($key);
        if ($this->isMethodExists($getter))
            return $this->$getter();
        elseif ($this->isMethodExists('set' . sanitize_camelcase($key)))
            throw new \Exception(sprintf(
                'The Property "%s" is writeonly.'
                , $key
            ));
        else throw new \Exception(sprintf(
            'The Property "%s" is not found.'
            , $key
        ));
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        try {
            $this->__get($key);
        } catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->__set($key, null);
    }

    /**
     * Get Options Properties Information
     *
     * @return AbstractOptions\PropsObject
     */
    function props()
    {
        if ($this->_cachedProps)
            return $this->_cachedProps;

        $ref     = new \ReflectionClass($this);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        $props   = [];
        foreach($methods as $i => $method)
            if (! in_array($prefix = substr($method->getName(), 0, 3), ['set', 'get']))
                // this is not property method
                unset($methods[$i]);
            else
                $props[$prefix][] = strtolower(Core\sanitize_underscore(
                    str_replace($prefix, '', $method->getName())
                ));

        $this->_cachedProps = new AbstractOptions\PropsObject($props);

        return $this->props();
    }

    /**
     * Is Setter Property Method?
     *
     * @param string $method Method Name
     *
     * @return bool
     */
    protected function isMethodExists($method)
    {
        $return = method_exists($this, $method);
        if ($return) {
            $ref = new \ReflectionMethod($this, $method);
            $return = $return && $ref->isPublic();
        }

        return $return;
    }
}
