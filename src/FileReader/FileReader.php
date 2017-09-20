<?php

namespace Config\FileReader;

use Config\Exception\ConfigurationFileException;

abstract class FileReader
{
    protected function validateFile(string $file)
    {
        if (!is_file($file)) {
            throw new ConfigurationFileException($file, 0 , null, ' file does not exist');
        }

        if (!is_readable($file)) {
            throw new ConfigurationFileException($file, 0 , null, ' is not readable');
        }
    }

    /**
     * Returns an array of extensions this class can read
     *
     * @return array
     */
    abstract function getExtensions(): array;

    /**
     * Reads the file and returns the data contained there in
     *
     * @param string $file Full path to file
     * @return array
     */
    abstract function read(string $file): array;
}