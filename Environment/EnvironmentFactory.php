<?php
namespace Poirot\Std\Environment;

use Poirot\Std\Interfaces\Pact\ipFactory;

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
     * !! callable: string|BaseEnv function()
     *
     * @param string|callable $aliasOrCallable
     *
     * @throws \Exception
     * @return BaseEnv
     */
    static function of($aliasOrCallable)
    {
        $alias = $aliasOrCallable;

        if (is_callable($alias))
            $alias = call_user_func($aliasOrCallable);

        if ($alias instanceof BaseEnv)
            ## Callable return Environment Instance
            return $alias;

        elseif (!is_string($alias))
            throw new \Exception(sprintf(
                'Invalid Alias name provided. it must be string given: %s.'
                , (is_callable($aliasOrCallable))
                  ? \Poirot\Std\flatten($alias).' provided from Callable' : \Poirot\Std\flatten($alias)
            ));

        ## find alias names: dev->development ==> class_name
        $EnvClass = null;
        while(isset(self::$_aliases[$alias]))
            $EnvClass = $alias = self::$_aliases[$alias];

        if (!class_exists($EnvClass))
            throw new \Exception("Class map for {$alias} environment not implemented.");

        return new $alias;
    }
}
