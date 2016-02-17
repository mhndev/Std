<?php
namespace Poirot\Std\PHPEnv;
use Poirot\Std\PHPEnv;

/**
 * - Display Errors Off
 * - Advised to use error logging in place of error displaying on production
 *
 */

class ESProduction extends PHPEnv
{
    /**
     * @return mixed
     */
    function getDisplayErrors()
    {
        if($this->errorReporting === null)
            $this->setDisplayErrors(0);

        return $this->displayErrors;
    }

    /**
     * @return mixed
     */
    function getErrorReporting()
    {
        if($this->errorReporting === null)
            ## we will do our own error handling
            $this->setErrorReporting(0);

        return $this->errorReporting;
    }

    /**
     * @return mixed
     */
    function getDisplayStartupErrors()
    {
        if($this->displayStartupErrors === null)
            $this->setDisplayStartupErrors(0);

        return $this->displayStartupErrors;
    }
}
