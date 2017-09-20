<?php

namespace Config\FileReader;

use Config\Exception\ConfigurationFileException;

class PhpReader extends FileReader
{
    function read(string $file): array
    {
        $this->validateFile($file);
        $value = include ($file);
        if (is_array($value)) {
            return $value;
        }
        throw new ConfigurationFileException($file, 0, null, ' php config file must return an array');
    }

    function getExtensions(): array
    {
        return ['php'];
    }
}