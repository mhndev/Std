<?php
namespace Poirot\Std\AbstractOptions;

final class PropsObject
{
    ## read/write options
    public $complex  = [];
    public $readable = [];
    public $writable = [];

    function __construct(array $props)
    {
        if (array_key_exists('writable', $props))
            $this->writable = $props['writable'];

        if (array_key_exists('readable', $props))
            $this->readable = $props['readable'];

        if (is_array($this->readable) && is_array($this->writable))
            $this->complex = array_intersect($this->readable, $this->writable);
    }
}
