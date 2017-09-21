<?php

namespace Conphig\FileReader;

use Conphig\Exception\ConfigurationFileException;

class IniReader extends FileReader
{
    public function read(string $file): array
    {
        $this->validateFile($file);

        try {
            return parse_ini_file($file, true, INI_SCANNER_TYPED);
        } catch (\Throwable $e) {
            throw new ConfigurationFileException($file, $e->getCode(), $e, $e->getMessage());
        }
    }

    public function getExtensions(): array
    {
        return ['ini'];
    }
}
