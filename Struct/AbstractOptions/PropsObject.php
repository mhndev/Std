<?php
namespace Poirot\Std\Struct\AbstractOptions;

final class PropsObject
{
    /** @var string property_name */
    protected $name;

    protected $readable   = 001;
    protected $writable   = 010;
    protected $accumulate = 000; // its r/w 011

    /**
     * PropsObject constructor.
     * @param string $propertyName
     */
    function __construct($propertyName)
    {
        $this->name = (string) $propertyName;
    }

    function getName()
    {
        return $this->name;
    }

    function __toString()
    {
        return $this->getName();
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
