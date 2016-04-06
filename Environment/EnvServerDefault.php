<?php
namespace Poirot\Std\Environment;

class EnvServerDefault extends EnvBase
{
    /**
     * @return mixed
     */
    function getDisplayErrors()
    {
        if($this->errorReporting === null)
            $this->setDisplayErrors( (int) ini_get('display_errors'));

        return $this->displayErrors;
    }

    /**
     * @return mixed
     */
    function getErrorReporting()
    {
        if ($this->errorReporting === null)
            ## current php settings
            $this->setErrorReporting( (int) ini_get('error_reporting'));

        return $this->errorReporting;
    }

    /**
     * @return mixed
     */
    function getDisplayStartupErrors()
    {
        if($this->displayStartupErrors === null)
            $this->setDisplayStartupErrors( (int) ini_get('display_startup_errors'));

        return $this->displayStartupErrors;
    }
}
