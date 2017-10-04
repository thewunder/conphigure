<?php

namespace Conphigure\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Loader;

/**
 * Reads from .env files, unlike reading directly with the dotenv library, it will not set $_ENV or $_SERVER globals
 */
class EnvReader extends Loader implements FileReaderInterface
{
    public function __construct($filePath = '', $immutable = false)
    {
        parent::__construct($filePath, $immutable);
    }

    public function getExtensions(): array
    {
        return ['env'];
    }

    public function read(string $file): array
    {
        $lines = $this->readLinesFromFile($file);
        $config = [];
        foreach ($lines as $line) {
            if (!$this->isComment($line) && $this->looksLikeSetter($line)) {
                try {
                    list($name, $value) = $this->normaliseEnvironmentVariable($line, null);
                    $config[$name] = $value;
                } catch (InvalidFileException $e) {
                    throw new ConfigurationFileException($file, $e->getCode(), $e, $e->getMessage());
                }
            }
        }
        return $config;
    }
}