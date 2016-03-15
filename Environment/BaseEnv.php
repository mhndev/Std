<?php
namespace Poirot\Std\Environment;

use Poirot\Std\Struct\AbstractOptionsData;

/*

ESProduction::setupSystemWide();
# warning error not displayed
echo $not_defined_variable;

*/

class BaseEnv extends AbstractOptionsData
{
    protected $displayErrors;
    protected $errorReporting;
    protected $displayStartupErrors;
    protected $htmlErrors;

    /**
     * Setup Php Environment
     *
     * $settings will override default environment values
     *
     * @param BaseEnv|array|\Traversable $settings
     */
    static function setupSystemWide($settings = null)
    {
        $self = new static;

        if ($settings !== null)
            $self->from($settings);

        // use properties

        foreach($self as $prop => $value) {
            switch ($prop) {
                case 'display_errors':
                    ini_set('display_errors', $value);
                    break;
                case 'error_reporting':
                    error_reporting($value);
                    break;
            }
        }

        $self->doInitSystem();
    }

    protected function doInitSystem()
    {
        // specific system wide setting initialize ...
    }

    // ...

    /**
     * @param mixed $displayErrors
     * @return $this
     */
    function setDisplayErrors($displayErrors)
    {
        $this->displayErrors = $displayErrors;
        return $this;
    }

    /**
     * @return mixed
     */
    function getDisplayErrors()
    {
        return $this->displayErrors;
    }

    /**
     * @param int $errorReporting
     * @return $this
     */
    function setErrorReporting($errorReporting)
    {
        $this->errorReporting = $errorReporting;
        return $this;
    }

    /**
     * @return mixed
     */
    function getErrorReporting()
    {
        return $this->errorReporting;
    }

    /**
     * @param mixed $displayStartupErrors
     * @return $this
     */
    function setDisplayStartupErrors($displayStartupErrors)
    {
        $this->displayStartupErrors = $displayStartupErrors;
        return $this;
    }

    /**
     * @return mixed
     */
    function getDisplayStartupErrors()
    {
        return $this->displayStartupErrors;
    }

    /**
     * @param mixed $htmlErrors
     * @return $this
     */
    function setHtmlErrors($htmlErrors)
    {
        $this->htmlErrors = $htmlErrors;
        return $this;
    }

    /**
     * @return mixed
     */
    function getHtmlErrors()
    {
        return $this->htmlErrors;
    }
}
