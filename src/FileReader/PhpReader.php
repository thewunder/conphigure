<?php

namespace Config\FileReader;

class PhpReader extends FileReader
{
    function read(string $file): array
    {
        $this->validateFile($file);
        return include ($file);
    }

    function getExtensions(): array
    {
        return ['php'];
    }
}