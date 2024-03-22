<?php

namespace Conphigure;

use ArrayAccess;
use Conphigure\Exception\ConphigureException;
use Conphigure\Exception\ConfigurationFileException;
use Conphigure\Exception\ConfigurationMissingException;
use Conphigure\FileReader\EnvReader;
use Conphigure\FileReader\FileReaderInterface;
use Conphigure\FileReader\IniReader;
use Conphigure\FileReader\JsonReader;
use Conphigure\FileReader\PhpReader;
use Conphigure\FileReader\XmlReader;
use Conphigure\FileReader\YamlReader;
use Conphigure\FileReader\DirectoryReader;
use Dotenv\Dotenv;
use Symfony\Component\Yaml\Parser;

/**
 * Main entry point to the Conphigure library
 */
class Conphigure implements ArrayAccess
{
    public const DEFAULT_DELIMITER = '/';

    private array $config = [];
    private array $fileReaders;


    /**
     * @param FileReaderInterface[] $fileReaders
     * @param string $delimiter Character used to separate nested levels of configuration
     */
    public function __construct(array $fileReaders, private readonly string $delimiter = self::DEFAULT_DELIMITER)
    {
        foreach ($fileReaders as $fileReader) {
            $this->addFileReader($fileReader);
        }
    }

    /**
     * @param FileReaderInterface[] $fileReaders If empty, all bundled file readers will be added
     * @param string $delimiter Character used to separate nested levels of configuration
     * @param bool $prefixFiles Set contents of each file with a prefix based on the file name when reading directories
     * @return Conphigure
     */
    public static function create(array $fileReaders = [], string $delimiter = self::DEFAULT_DELIMITER, bool $prefixFiles = true): self
    {
        if (empty($fileReaders)) {
            $fileReaders = self::allReaders();
        }
        $config = new self($fileReaders, $delimiter);
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
        $readers = [new PhpReader(), new IniReader()];
        if (extension_loaded('json')) {
            $readers[] = new JsonReader();
        }
        if (extension_loaded('SimpleXML')) {
            $readers[] = new XmlReader();
        }
        if (class_exists(Parser::class)) {
            $readers[] = new YamlReader(new Parser());
        }
        if (class_exists(Dotenv::class)) {
            $readers[] = new EnvReader();
        }
        return $readers;
    }

    /**
     * Retrieves a value from config, a ConfigurationMissingException is thrown if the value is not found and no default is provided
     *
     * @param string $key Key delimited by the separator (ex. system/db/host)
     * @param mixed|null $default If a non-null value is passed the default will be returned instead of an exception being thrown
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
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
     */
    public function set(string $key, mixed $value): void
    {
        $keyParts = $this->getKeyParts($key);
        $this->recursiveSet($keyParts, $this->config, $value);
    }

    private function recursiveSet(array $keyParts, array &$config, $value): void
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
    public function remove(string $key): void
    {
        $keyParts = $this->getKeyParts($key);
        $this->recursiveRemove($keyParts, $this->config);
    }

    private function recursiveRemove(array $keyParts, array &$config): void
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
     */
    public function addConfiguration(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    public function addFileReader(FileReaderInterface $fileReader): void
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
        throw new ConfigurationFileException($path, 0, null, " no file reader for $key available");
    }

    /**
     * Validates that the path exists and is readable
     */
    protected function validatePath(string $file): void
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
     * @return array
     */
    private function getKeyParts(string $key): array
    {
        $keyParts = explode($this->delimiter, $key);
        $keyParts = array_filter($keyParts, fn(string $keyPart) => !empty($keyPart));
        return array_values($keyParts);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }
}
