<?php

namespace Conphig\FileReader;

use Conphig\Exception\ConfigurationFileException;

class PhpReader extends FileReader
{
    public function read(string $file): array
    {
        $value = include($file);
        if (is_array($value)) {
            return $value;
        }
        throw new ConfigurationFileException($file, 0, null, ' php config file must return an array');
    }

    public function getExtensions(): array
    {
        return ['php'];
    }
}
