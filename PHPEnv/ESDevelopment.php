<?php
namespace Poirot\Std\PHPEnv;

use Poirot\Std\PHPEnv;

/**
 * - Enabling E_NOTICE, E_STRICT Error Messages
 *
 */

class ESDevelopment extends PHPEnv
{
    /** PHP 5.3 or later, the default value is E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED */
    protected $errorReporting;

    /**
     * @return mixed
     */
    function getDisplayErrors()
    {
        if($this->errorReporting === null)
            $this->setDisplayErrors(1);

        return $this->displayErrors;
    }

    /**
     * @return mixed
     */
    function getErrorReporting()
    {
        if($this->errorReporting === null)
            $this->setErrorReporting(E_ALL);

        return $this->errorReporting;
    }

    /**
     * @return mixed
     */
    function getDisplayStartupErrors()
    {
        if($this->displayStartupErrors === null)
            $this->setDisplayStartupErrors(1);

        return $this->displayStartupErrors;
    }
}
