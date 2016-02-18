<?php
namespace Poirot\Std;

use Poirot\Std\Interfaces\Struct\iDataEntity;
use Poirot\Std\Struct\Entity;

class Config extends Entity
{
    /**
     * Set Properties
     *
     * - You can implement this method on subclasses
     *
     * @param iDataEntity $resource
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function __setFrom($resource)
    {
        if (is_string($resource)) {
            if (is_file($resource))
                $this->fromFile($resource);
            else
                $this->fromDir($resource);

            return;
        }

        if (is_array($resource) && array_values($resource) === $resource) {
            # this is list of files
            $this->fromFiles($resource);

            return;
        }

        return parent::__setFrom($resource);
    }

    /**
     * Set Options From Config Directory
     *
     * @param string $dirPath path to config files directory
     *
     * @throws \Exception
     * @return $this
     */
    function fromDir($dirPath)
    {
        if (!is_dir($dirPath))
            throw new \Exception("Directory ({$dirPath}) not found.");

        $conFiles = $dirPath.'/*.{,local.}conf.php';
        $this->fromFiles(glob($conFiles, GLOB_BRACE));

        return $this;
    }

    /**
     * Set Options From Config File
     *
     * @param array $files path to files
     *
     * @throws \Exception
     * @return $this
     */
    function fromFiles(array $files)
    {
        foreach ($files as $f)
            $this->fromFile($f);

        return $this;
    }

    /**
     * Set Options From Config File
     *
     * @param string $filePath path to config file
     *
     * @throws \Exception
     * @return $this
     */
    function fromFile($filePath)
    {
        if (!is_file($filePath))
            throw new \Exception("Path To file ({$filePath}) not found.");

        ErrorStack::handleError(E_ALL ^ E_WARNING, function ($error, $message = '', $file = '', $line = 0) {
            ob_end_clean();
            throw new \RuntimeException(sprintf(
                'Error loading config: %s::%s %s'
                , $file, $line, $message
            ), $error);
        });

        ob_start();
        $config   = include_once $filePath;
        ob_get_clean();

        ErrorStack::handleDone();

        $this->fromArray($config);
        return $this;
    }
}
