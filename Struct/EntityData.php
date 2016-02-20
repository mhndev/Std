<?php
namespace Poirot\Std\Struct;

!defined('POIROT_CORE_LOADED') and include_once __DIR__.'/../functions.php';

use Traversable;

use Poirot\Std;

/*
$data = function() {
    yield [0,1]            => ['this is data for this column'];
    yield new \Directory() => 'this is value for Directory as a key.';
};

$entity = new Entity($data());

$entity->set([0,1], ['this is data for this column']);
$entity->set(new \Directory(), 'this is value for Directory as a key.');

foreach($entity as $k => $v)
        // $k: array, \Directory
*/


class EntityData extends AbstractDataStruct
    implements Std\Interfaces\Struct\iEntityData
{
    /** @var array Key/Values */
    protected $_properties = [];

    /** @var array Mapped None scalar as keys */
    protected $__mapedPropObjects = [
        # 'hash_string' => $none_scalar_key_self
    ];


    /**
     * Set Struct Data From Array
     *
     * @param array|\Traversable $data
     */
    function doSetFrom($data)
    {
        foreach($data as $k => $v)
            $this->set($k, $v);
    }

    /**
     * Set Entity
     *
     * - values that set to null must be unset from entity
     *
     * @param mixed      $key   Entity Key
     * @param mixed|null $value Entity Value
     *                          NULL value for a property considered __isset false
     *
     * @return $this
     */
    function set($key, $value = VOID)
    {
        if ($key !== $hash = $this->__normalizeKey($key)) {
            $propObj = $key;
            $key     = $hash;
            ## store object map
            $this->__mapedPropObjects[$key] = $propObj;
        }


        if ($value === null)
            $this->del($key);
        else
            $this->__attainDataArrayReference()[$key] = $value;

        return $this;
    }

    /**
     * Get Entity Value
     *
     * @param mixed $key     Entity Key
     * @param null  $default Default If Not Value/Key Exists
     *
     * @throws \Exception Value not found
     * @return mixed|null NULL value for a property considered __isset false
     */
    function get($key, $default = null)
    {
        $key = $this->__normalizeKey($key);

        // avoid recursive trait call, may conflict on classes that
        // implement in this case has() method
        if (
            !array_key_exists($key, $this->__attainDataArrayReference())
            && $default === null
        )
            throw new \Exception(sprintf(
                'Property (%s) not found in entity.', $key
            ));

        return (array_key_exists($key, $this->__attainDataArrayReference()))
            ? $this->__attainDataArrayReference()[$key]
            : $default;
    }

    /**
     * Has Property
     *
     * @param string $key Property
     *
     * @return boolean
     */
    function has($key)
    {
        $key = $this->__normalizeKey($key);
        return array_key_exists($key, $this->__attainDataArrayReference());
    }

    /**
     * Delete a property
     *
     * @param string $key Property
     *
     * @return $this
     */
    function del($key)
    {
        if ($key !== $hash = $this->__normalizeKey($key))
            $key = $hash;

        if ( array_key_exists($key, $this->__attainDataArrayReference()) ) {
            unset($this->__attainDataArrayReference()[$key]);
            unset($this->__mapedPropObjects[$hash]);
        }

        return $this;
    }


    // ...

    /**
     * Make hash string for none scalars
     * @param string|mixed $key
     * @return string
     */
    protected function __normalizeKey($key)
    {
        if (!is_string($key) && !is_numeric($key))
            $key = md5(Std\flatten($key));

        return $key;
    }

    /**
     * @return array
     */
    protected function &__attainDataArrayReference()
    {
        return $this->_properties;
    }


    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    function getIterator()
    {
        $data   = $this->__attainDataArrayReference();
        foreach($data as $k => $v) {
            (!array_key_exists($k, $this->__mapedPropObjects))
                ?: $k = $this->__mapedPropObjects[$k];

            yield $k => $v;
        }
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
        return count($this->_properties);
    }
}
