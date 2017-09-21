<?php

namespace Config\FileReader;

use Config\Exception\ConfigurationFileException;

/**
 * Reads configuration data from an individual file
 */
abstract class FileReader
{
    /**
     * Validates that the string is a readable file
     *
     * @param string $file
     */
    protected function validateFile(string $file)
    {
        if (!is_file($file)) {
            throw new ConfigurationFileException($file, 0, null, ' file does not exist');
        }

        if (!is_readable($file)) {
            throw new ConfigurationFileException($file, 0, null, ' is not readable');
        }
    }

    /**
     * Returns an array of extensions this class can read
     *
     * @return array
     */
    abstract public function getExtensions(): array;

    /**
     * Reads the file and returns the data contained there in
     *
     * @param string $file Full path to file
     * @return array
     */
    abstract public function read(string $file): array;
}
