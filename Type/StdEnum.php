<?php
namespace Poirot\Std\Type;

if (!class_exists('\SplEnum')) {
    require __DIR__.'/fixes/NSplEnum.php';
    class_alias('\Poirot\Std\Type\NSplEnum', '\SplEnum');
}

class StdEnum extends \SplEnum
{

}
