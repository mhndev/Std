<?php
namespace Poirot\Std\Environment;

use Poirot\Std\Struct\AbstractOptions;

/*

ESProduction::setupSystemWide();
# warning error not displayed
echo $not_defined_variable;

*/

class BaseEnv extends AbstractOptions
{
    protected $displayErrors;
    protected $errorReporting;
    protected $displayStartupErrors;
    protected $htmlErrors;

    /**
     * Setup Php Environment With Given Settings
     *
     * @param BaseEnv $settings
     */
    static function setupSystemWide(BaseEnv $settings = null)
    {
        if ($settings === null)
            $settings = new static;

        foreach($settings->__props()->readable as $prop) {
            switch ($prop) {
                case 'display_errors':
                    ini_set('display_errors', $settings->__get($prop));
                    break;
                case 'error_reporting':
                    error_reporting($settings->__get($prop));
                    break;
            }
        }

        $settings->init();
    }

    protected function init()
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
