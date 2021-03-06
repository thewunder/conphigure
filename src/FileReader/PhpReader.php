<?php

namespace Conphigure\FileReader;

use Conphigure\Exception\ConfigurationFileException;

/**
 * Reads configuration from .php files that return an array
 */
class PhpReader implements FileReaderInterface
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
