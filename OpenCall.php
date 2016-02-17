<?php
namespace Poirot\Std;

use Poirot\Std\Traits\OpenCallTrait;

class OpenCall
{
    use OpenCallTrait;

    /**
     * Construct
     *
     * @param null  $bindTo
     * @param array $methods ['method_name' => \Closure]
     *
     * @throws \Exception
     */
    function __construct($bindTo = null, array $methods = [])
    {
        if (!is_object($bindTo) && $bindTo !== null)
            throw new \InvalidArgumentException(sprintf(
                'Invalid argument BindTo for OpenCall Construct, must given Object instead "%s" given.'
                , \Poirot\Std\flatten($bindTo)
            ));

        if ($bindTo)
            $this->bindTo($bindTo);

        foreach($methods as $m => $f)
            $this->addMethod($m, $f);
    }
}
