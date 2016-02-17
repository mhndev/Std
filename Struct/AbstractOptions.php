<?php
namespace Poirot\Std\Struct;

use Poirot\Std;
use Poirot\Std\Interfaces\Struct\iOptionStruct;

/**
 * Here is a simple optionsClass example:
 *
 * ~~~
 * class Options extend AbstractOptions {
 *
 *  // >>> Plug(Full) Properties >>>>>
 *
 *  // >>> Not Set Yet By Default And Not List On ToArray >>>>>
 *  protected $name = VOID;
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
abstract class AbstractOptions
    implements iOptionStruct
{
    use Std\Struct\Traits\OptionsTrait;

    /**
     * Construct
     *
     * @param array|iOptionStruct $options Options
     */
    function __construct($options = null)
    {
        if ($options !== null)
            $this->from($options);
    }
}
