<?php
namespace Poirot\Core;

use ErrorException;

/*
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
     * @param int $errorLevel
     * @param callable|null $callable
     */
    static function handleBegin($errorLevel = self::ERR_DEF_SEVERITY, callable $callable = null)
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
        ];
    }

    /**
     * Stopping the error handler
     *
     * @return null|ErrorException
     */
    static function handleDone()
    {
        $return = null;

        if (!self::hasHandling())
            ## there is no error
            return $return;

        restore_error_handler();

        $stack = array_pop(self::$_STACK);
        if ($stack['has_error'])
            $return = $stack['has_error'];

        return $return;
    }

    /**
     * Stop all active handler and clean stack
     *
     */
    static function clean()
    {
        restore_error_handler();

        self::$_STACK = [];
    }

    static function _handleError($errno, $errstr = '', $errfile = '', $errline = 0)
    {
        $stack = & self::$_STACK[self::getLevel()-1];
        $stack['has_error'] = new ErrorException($errstr, 0, $errno, $errfile, $errline);

        if (isset($stack['callable']))
            ## call user error handler callable
            call_user_func($stack['callable'], $errno, $errstr, $errfile, $errline);
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
