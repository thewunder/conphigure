<?php

namespace Config;

use Config\Exception\ConfigurationFileException;
use Config\Exception\ConfigurationMissingException;
use Config\FileReader\FileReader;
use Config\FileReader\IniReader;
use Config\FileReader\JsonReader;
use Config\FileReader\PhpReader;
use Config\FileReader\YamlReader;
use Config\FileReader\DirectoryReader;
use Psr\SimpleCache\CacheInterface;

/**
 * Main entry point to the Config library
 */
class ConfigurationManager
{
    const DEFAULT_SEPARATOR = '/';

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
    public function __construct(array $fileReaders, string $separator = self::DEFAULT_SEPARATOR)
    {
        foreach ($fileReaders as $fileReader) {
            $this->addFileReader($fileReader);
        }
        $this->separator = $separator;
    }

    public static function create(array $fileReaders = [], $separator = self::DEFAULT_SEPARATOR, bool $prefixFiles = true): self
    {
        if (empty($fileReaders)) {
            $fileReaders = self::allReaders();
        }
        $config = new self($fileReaders, $separator);
        $directoryReader = new DirectoryReader($config, $prefixFiles);
        $config->addFileReader($directoryReader);
        return $config;
    }

    /**
     * @return FileReader[]
     */
    protected static function allReaders(): array
    {
        $readers = [new PhpReader(), new JsonReader(), new IniReader()];
        if (class_exists('Symfony\\Component\\Yaml\\Parser')) {
            $readers[] = new YamlReader();
        }
        return $readers;
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
     * Retrieves all configuration information as an array
     *
     * @return array
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Reads all configuration from the provided path
     *
     * @param string $path Full path to file or directory
     */
    public function read(string $path)
    {
        $reader = $this->getFileReader($path);
        $this->addConfiguration($reader->read($path));
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
     * @param string $path A file path, the extension is used to determine which file reader is loaded
     * @return FileReader
     */
    public function getFileReader(string $path): FileReader
    {
        if (is_dir($path)) {
            $key = '/';
        } else {
            $key = pathinfo($path, PATHINFO_EXTENSION);
        }

        if (isset($this->fileReaders[$key])) {
            return $this->fileReaders[$key];
        }
        throw new ConfigurationFileException($path, 0, null, " no file reader for {$key} available");
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
