<?php
namespace Poirot\Std\Environment;

use Poirot\Std\Interfaces\ipFactory;

/*

$EnvSettings = AliasEnvFactory::with(function() {
    $default = ($env_mode = getenv('POIROT_ENV_MODE')) ? $env_mode : 'default';
    return (defined('DEBUG') && constant('DEBUG')) ? 'dev' : $default;
});
$EnvSettings::setupSystemWide();

*/

class EnvironmentFactory implements ipFactory
{
    protected static $_aliases = [
        'development'     => \Poirot\Std\Environment\DevelopmentEnv::class,
        'dev'             => 'development',
        'debug'           => 'development',

        'production'      => \Poirot\Std\Environment\ProductionEnv::class,
        'prod'            => 'production',

        'php-environment' => \Poirot\Std\Environment\PhpCurrentEnv::class,
        'php'             => 'php-environment',
        'default'         => 'php',
    ];

    /**
     * Factory To Settings Environment
     *
     * @param string|callable $aliasOrCallable
     *
     * @throws \Exception
     * @return BaseEnv
     */
    static function of($aliasOrCallable)
    {
        if (is_callable($aliasOrCallable))
            $aliasOrCallable = call_user_func($aliasOrCallable);

        while(isset(self::$_aliases[$aliasOrCallable]))
            $aliasOrCallable = self::$_aliases[$aliasOrCallable];

        if (!class_exists($aliasOrCallable))
            throw new \Exception("Settings for {$aliasOrCallable} not implemented.");

        return new $aliasOrCallable;
    }
}
