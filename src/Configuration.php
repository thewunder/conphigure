<?php

namespace Conphig;

use Conphig\Exception\ConfigurationFileException;
use Conphig\Exception\ConfigurationMissingException;
use Conphig\FileReader\FileReader;
use Conphig\FileReader\IniReader;
use Conphig\FileReader\JsonReader;
use Conphig\FileReader\PhpReader;
use Conphig\FileReader\YamlReader;
use Conphig\FileReader\DirectoryReader;

/**
 * Main entry point to the Config library
 */
class Configuration
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
     * @var array
     */
    private $fileReaders;


    /**
     * Configuration constructor.
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
     * Retrieves a value from config, a ConfigurationMissingException is thrown if the value is not found and no default is provided
     *
     * @param string $key
     * @param mixed $default If a non-null value is passed the default will be returned instead of an exception being thrown
     * @return mixed
     */
    public function get(string $key, $default = null)
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

        if($default !== null) {
            return $default;
        } else {
            throw new ConfigurationMissingException($key);
        }
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
        $this->validatePath($path);
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
        $this->config = array_merge($this->config, $config);
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
     * Validates that the path exists and is readable
     *
     * @param string $file
     */
    protected function validatePath(string $file)
    {
        if (!file_exists($file)) {
            throw new ConfigurationFileException($file, 0, null, ' does not exist');
        }

        if (!is_readable($file)) {
            throw new ConfigurationFileException($file, 0, null, ' is not readable');
        }
    }
}
