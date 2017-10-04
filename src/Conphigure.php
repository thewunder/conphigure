<?php

namespace Conphigure;

use Conphigure\Exception\ConphigureException;
use Conphigure\Exception\ConfigurationFileException;
use Conphigure\Exception\ConfigurationMissingException;
use Conphigure\FileReader\EnvReader;
use Conphigure\FileReader\FileReaderInterface;
use Conphigure\FileReader\IniReader;
use Conphigure\FileReader\JsonReader;
use Conphigure\FileReader\PhpReader;
use Conphigure\FileReader\YamlReader;
use Conphigure\FileReader\DirectoryReader;

/**
 * Main entry point to the Conphigure library
 */
class Conphigure implements \ArrayAccess
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
     * @param FileReaderInterface[] $fileReaders
     * @param string $separator Character to separate complex configuration keys
     */
    public function __construct(array $fileReaders, string $separator = self::DEFAULT_SEPARATOR)
    {
        foreach ($fileReaders as $fileReader) {
            $this->addFileReader($fileReader);
        }
        $this->separator = $separator;
    }

    /**
     * @param FileReaderInterface[] $fileReaders If empty, all bundled file readers will be added
     * @param string $separator Separator for reading configuration paths
     * @param bool $prefixFiles Set contents of each file with a prefix based on the file name when reading directories
     * @return Conphigure
     */
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
     * Creates all bundled file readers
     *
     * @return FileReaderInterface[]
     */
    protected static function allReaders(): array
    {
        $readers = [new PhpReader(), new JsonReader(), new IniReader()];
        if (class_exists('Symfony\\Component\\Yaml\\Parser')) {
            $readers[] = new YamlReader();
        }
        if (class_exists('Dotenv\\Loader')) {
            $readers[] = new EnvReader();
        }
        return $readers;
    }

    /**
     * Retrieves a value from config, a ConfigurationMissingException is thrown if the value is not found and no default is provided
     *
     * @param string $key Key delimited by the separator (ex. system/db/host)
     * @param mixed $default If a non-null value is passed the default will be returned instead of an exception being thrown
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $keyParts = $this->getKeyParts($key);
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

        if ($default !== null) {
            return $default;
        } else {
            throw new ConfigurationMissingException($key);
        }
    }

    /**
     * @param string $key Key delimited by the separator (ex. system/db/host)
     * @return bool True if key exists
     */
    public function has(string $key): bool
    {
        $keyParts = $this->getKeyParts($key);
        $config = $this->config;
        foreach ($keyParts as $keyPart) {
            if (!isset($config[$keyPart])) {
                return false;
            }

            $config = $config[$keyPart];
        }

        return true;
    }

    /**
     * Sets a configuration value
     *
     * @param string $key Key delimited by the separator (ex. system/db/host)
     * @param mixed $value
     */
    public function set(string $key, $value)
    {
        $keyParts = $this->getKeyParts($key);
        $this->recursiveSet($keyParts, $this->config, $value);
    }

    private function recursiveSet(array $keyParts, array &$config, $value)
    {
        $keyPart = array_shift($keyParts);
        if (empty($keyParts)) {
            $config[$keyPart] = $value;
        } else {
            if (!isset($config[$keyPart])) {
                $config[$keyPart] = [];
            } elseif (!is_array($config[$keyPart])) {
                throw new ConphigureException("Refusing to overwrite existing non-array value at '$keyPart'");
            }
            $this->recursiveSet($keyParts, $config[$keyPart], $value);
        }
    }

    /**
     * Removes a configuration value
     *
     * @param string $key Key delimited by the separator (ex. system/db/host)
     */
    public function remove(string $key)
    {
        $keyParts = $this->getKeyParts($key);
        $this->recursiveRemove($keyParts, $this->config);
    }

    private function recursiveRemove(array $keyParts, array &$config)
    {
        $keyPart = array_shift($keyParts);
        if (empty($keyParts)) {
            unset($config[$keyPart]);
        } else {
            if (isset($config[$keyPart]) && is_array($config[$keyPart])) {
                $this->recursiveRemove($keyParts, $config[$keyPart]);
            }
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
     * Reads all configuration from the provided path, may overwrite existing configuration
     *
     * @param string $path Full path to file or directory
     * @param string $prefix Optional prefix to add (delimited by the delimiter)
     *
     * @return array Configuration read from the specified path
     */
    public function read(string $path, string $prefix = ''): array
    {
        $this->validatePath($path);
        $reader = $this->getFileReader($path);
        $config = $reader->read($path);
        if ($prefix) {
            $this->set($prefix, $config);
        } else {
            $this->addConfiguration($config);
        }
        return $config;
    }

    /**
     * Adds configuration, configuration added later overrides values provided from previous sources
     *
     * @param array $config
     */
    public function addConfiguration(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param FileReaderInterface $fileReader
     */
    public function addFileReader(FileReaderInterface $fileReader)
    {
        foreach ($fileReader->getExtensions() as $extension) {
            $this->fileReaders[$extension] = $fileReader;
        }
    }

    /**
     * @param string $path A file path, the extension is used to determine which file reader is loaded
     * @return FileReaderInterface
     */
    public function getFileReader(string $path): FileReaderInterface
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

    /**
     * Splits a key up into an array of key parts
     *
     * @param string $key
     * @return array
     */
    private function getKeyParts(string $key): array
    {
        $keyParts = explode($this->separator, $key);
        $keyParts = array_filter($keyParts, function(string $keyPart){
            return !empty($keyPart);
        });
        return array_values($keyParts);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
