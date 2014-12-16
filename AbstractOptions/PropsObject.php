<?php
namespace Poirot\Core\AbstractOptions;

final class PropsObject
{
    public $plug = [];

    public $readable = [];

    public $writable = [];

    function __construct(array $props)
    {
        if (array_key_exists('set', $props))
            $this->writable = $props['set'];

        if (array_key_exists('get', $props))
            $this->readable = $props['get'];

        if (is_array($this->writable) && is_array($this->writable))
            $this->plug = array_intersect($this->writable, $this->writable);
    }
}
