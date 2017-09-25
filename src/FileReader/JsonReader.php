<?php

namespace Conphigure\FileReader;

use Conphigure\Exception\ConfigurationFileException;

class JsonReader implements FileReaderInterface
{
    public function getExtensions(): array
    {
        return ['json'];
    }

    public function read(string $file): array
    {
        try {
            return json_decode(file_get_contents($file), true);
        } catch (\Error $e) {
            throw new ConfigurationFileException($file, $e->getCode(), $e, $e->getMessage());
        }
    }
}
