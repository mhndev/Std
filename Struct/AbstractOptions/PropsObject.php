<?php
namespace Poirot\Std\Struct\AbstractOptions;

final class PropsObject
{
    /** @var string property_name */
    protected $key;

    protected $readable   = 001;
    protected $writable   = 010;
    protected $accumulate = 000; // its r/w 011

    /**
     * PropsObject constructor.
     * @param string $propertyName
     */
    function __construct($propertyName)
    {
        $this->key = (string) $propertyName;
    }

    function getKey()
    {
        return $this->key;
    }

    function __toString()
    {
        return $this->getKey();
    }

    function setReadable($flag = true)
    {
        $r = $this->readable;

        if ($flag)
            $this->accumulate |= $r;
        else
            $this->accumulate ^= $r;

        return $this;
    }

    function setWritable($flag = true)
    {
        $w = $this->writable;

        if ($flag)
            $this->accumulate |= $w;
        else
            $this->accumulate ^= $w;

        return $this;
    }

    function isReadable()
    {
        return (bool) ($this->accumulate & $this->readable);
    }

    function isWritable()
    {
        return (bool) ($this->accumulate & $this->writable);
    }
}
