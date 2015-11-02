<?php
namespace Poirot\Core;

use ErrorException;

/*
 *
ErrorStack::handleBegin(function($errno, $errstr = '', $errfile = '', $errline = 0) {
    ## this will print error string to output
    var_dump($errstr);
});

ErrorStack::handleBegin(E_USER_WARNING);
ErrorStack::rise('This is user warning error.', E_USER_WARNING);
$userError = ErrorStack::handleDone();
var_dump($userError);

# Error Happen
echo $not_defined_variable;

# return ErrorException or null if not any error
$error = ErrorStack::handleDone();
if ($error)
    throw $error;

// == Chain Exception Handlers ==========================================================

ErrorStack::handleException(function ($e) {
    ## then accrued exception rise to php default
    throw $e;
});

ErrorStack::handleException(function ($e) {
    echo 'next we see this<br/>';

    ## pass to next error handler by throwing exception
    throw $e;
});

ErrorStack::handleException(function ($e) {
    echo 'this error will appear once.<br/>';

    ## pass to next error handler by throwing exception
    throw $e;
});

## rise Exception
throw new \Exception;

*/

class ErrorStack
{
    const ERR_DEF_SEVERITY = E_ALL;

    protected static $_STACK = [
        # [
            # 'error_level' => int,
            # 'callable'    => null|callable,
            # 'has_error'   => null|ErrorException,
        # ]
    ];

    /**
     * Check if this error handler is active
     *
     * @return bool
     */
    static function hasHandling()
    {
        return (bool) self::getLevel();
    }

    /**
     * Get the current nested level
     *
     * @return int
     */
    static function getLevel()
    {
        return count(self::$_STACK);
    }

    /**
     * Used for defining your own way of handling errors during runtime,
     * for example in applications in which you need to do cleanup of -
     * data/files when a critical error happens, or when you need to -
     * trigger an error under certain conditions
     *
     * - handleBegin([$object, 'method'])
     *
     * @param callable|null $callable
     */
    static function handleException(callable $callable = null)
    {
        $self = get_called_class();
        set_exception_handler("{$self}::_handleError");

        self::$_STACK[] = [
            'error_level' => null,
            'callable'    => $callable,
            'has_error'   => null,
            '__handle__'  => 'exception',
        ];
    }

    /**
     * Used for defining your own way of handling errors during runtime,
     * for example in applications in which you need to do cleanup of -
     * data/files when a critical error happens, or when you need to -
     * trigger an error under certain conditions
     *
     * - handleError([$object, 'method'])
     *
     * @param int $errorLevel
     * @param callable|null $callable
     */
    static function handleError($errorLevel = self::ERR_DEF_SEVERITY, callable $callable = null)
    {
        if (is_callable($errorLevel)) {
            $callable   = $errorLevel;
            $errorLevel = self::ERR_DEF_SEVERITY;
        }

        $self = get_called_class();
        set_error_handler("{$self}::_handleError", $errorLevel);

        self::$_STACK[] = [
            'error_level' => $errorLevel,
            'callable'    => $callable,
            'has_error'   => null,
            '__handle__'  => 'error',
        ];
    }

    /**
     * Get Current Accured Error If Has
     *
     * @return null|\Exception|\ErrorException
     */
    static function getAccuredErr()
    {
        if (!self::hasHandling())
            return null;

        $stack = self::$_STACK[self::getLevel()-1];
        return $stack['has_error'];
    }

    /**
     * Stopping the error handler
     *
     * - return last error if it exists
     *
     * @return null|ErrorException
     */
    static function handleDone()
    {
        $return = null;

        if (!self::hasHandling())
            ## there is no error
            return $return;

        $stack = array_pop(self::$_STACK);
        if ($stack['has_error'])
            $return = $stack['has_error'];

        # restore error handler
        (($stack['__handle__']) == 'error') ? restore_error_handler() : restore_exception_handler();

        return $return;
    }

    /**
     * Stop all active handler and clean stack
     *
     */
    static function clean()
    {
        restore_error_handler();
        restore_exception_handler();

        self::$_STACK = [];
    }

    /**
     * Handle Both Exception And Errors That Happen Within
     * handleBegin/handleDone
     *
     * @private
     */
    static function _handleError($errno, $errstr = '', $errfile = '', $errline = 0)
    {
        $stack = & self::$_STACK[self::getLevel()-1];

        if (! $errno instanceof \Exception)
            ## handle errors
            $errno = new ErrorException($errstr, 0, $errno, $errfile, $errline);

        $stack['has_error'] = $errno;

        // ...

        if (isset($stack['callable']))
            try {
                $currLevel = self::getLevel();
                ## call user error handler callable
                ## exception will passed as errno on exception catch
                call_user_func($stack['callable'], $errno, $errstr, $errfile, $errline);
            }
            catch (\Exception $e) {
                ## during handling an error if any exception happen it must handle with parent handler
                if (self::getLevel() == $currLevel)
                    ## close current handler if not, the handleDone may be called from within handler callable
                    self::handleDone();

                if ($stack['__handle__'] == 'error')
                    ## Just throw exception, it might handled with exception handlers
                    throw $e;

                $isHandled = false;
                while (self::hasHandling()) {
                    $stack = & self::$_STACK[self::getLevel()-1];
                    if ($stack['__handle__'] == 'exception') {
                        $isHandled = true;
                        self::_handleError($e);
                        break;
                    }

                    self::handleDone();
                }

                if (!$isHandled)
                    ## throw exception if it not handled
                    throw $e;
            }
    }

    /**
     * Generates a user-level error
     *
     * @param string $message
     * @param int    $errorType
     *
     * @return bool
     */
    static function rise($message, $errorType = E_USER_NOTICE)
    {
        return trigger_error($message, $errorType);
    }
}
