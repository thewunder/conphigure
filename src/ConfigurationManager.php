<?php

namespace Config;

use Config\Exception\ConfigurationMissingException;
use Config\FileReader\FileReader;
use Config\Source\ConfigurationSource;
use Psr\SimpleCache\CacheInterface;

/**
 * Main entry point to the Config library
 */
class ConfigurationManager
{
    /**
     * @var array
     */
    private $config = [];
    /**
     * @var string
     */
    private $separator;
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var array
     */
    private $fileReaders;


    public function __construct(array $fileReaders, string $separator = '/')
    {
        $this->fileReaders = $fileReaders;
        $this->separator = $separator;
    }

    /**
     * Retrieves a value from config, a ConfigurationMissingException is thrown if the value is not found
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $keyParts = explode($this->separator, $key);
        $lastIndex = count($keyParts) - 1;
        $config = $this->config;
        foreach ($keyParts as $i => $keyPart) {
            if (!isset($config[$keyPart])) {
                break;
            }

            $config = $config[$keyPart];

            if ($i === $lastIndex) {
                return $config;
            }
        }
        throw new ConfigurationMissingException($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $keyParts = explode($this->separator, $key);
        $config = $this->config;
        foreach ($keyParts as $i => $keyPart) {
            if (!isset($config[$keyPart])) {
                return false;
            }

            $config = $config[$keyPart];
        }

        return true;
    }

    /**
     * Adds configuration, configuration added later override values provided from previous sources
     *
     * @param array $config
     */
    public function addConfiguration(array $config)
    {
        $this->config = array_merge_recursive($this->config, $config);
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }
}
