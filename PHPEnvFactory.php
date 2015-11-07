<?php
namespace Poirot\Core;

class PHPEnvFactory
{
    protected static $_aliases = [
        'development'     => 'Poirot\Core\PHPEnv\ESDevelopment',
        'dev'             => 'development',
        'debug'           => 'development',

        'production'      => 'Poirot\Core\PHPEnv\ESProduction',
        'prod'            => 'production',

        'php-environment' => 'Poirot\Core\PHPEnv\ESPhpEnvironment',
        'php'             => 'php-environment',
        'default'         => 'php',
    ];

    /**
     * Factory To Settings Environment
     *
     * @param string|callable $name
     *
     * @throws \Exception
     * @return PHPEnv
     */
    static function factory($name)
    {
        if (is_callable($name))
            $name = call_user_func($name);

        while(isset(self::$_aliases[$name]))
            $name = self::$_aliases[$name];

        if (!class_exists($name))
            throw new \Exception("Settings for {$name} not implemented.");

        return new $name;
    }
}
