<?php

namespace Config;

use Config\Exception\ConfigurationFileException;
use Config\Exception\ConfigurationMissingException;
use Config\FileReader\FileReader;
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


    /**
     * ConfigurationManager constructor.
     * @param FileReader[] $fileReaders
     * @param string $separator
     */
    public function __construct(array $fileReaders, string $separator = '/')
    {
        foreach ($fileReaders as $fileReader) {
            $this->addFileReader($fileReader);
        }
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
     * @param FileReader $fileReader
     */
    public function addFileReader(FileReader $fileReader)
    {
        foreach ($fileReader->getExtensions() as $extension) {
            $this->fileReaders[$extension] = $fileReader;
        }
    }

    /**
     * @param string $fileName A file path, the extension is used to determine which file reader is loaded
     * @return FileReader
     */
    public function getFileReader(string $fileName): FileReader
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        if (isset($this->fileReaders[$extension])) {
            return $this->fileReaders[$extension];
        }
        throw new ConfigurationFileException($fileName, 0, null, ' no file reader available');
    }

    /**
     * Adds a cache for enhanced performance
     *
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }
}
