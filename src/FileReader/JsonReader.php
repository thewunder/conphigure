<?php

namespace Config\FileReader;

use Config\Exception\ConfigurationFileException;

class JsonReader extends FileReader
{
    function getExtensions(): array
    {
        return ['json'];
    }

    function read(string $file): array
    {
        $this->validateFile($file);
        try {
            return json_decode(file_get_contents($file), true);
        } catch (\Error $e) {
            throw new ConfigurationFileException($file, $e->getCode(), $e, $e->getMessage());
        }
    }
}