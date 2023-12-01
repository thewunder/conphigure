<?php

namespace Conphigure\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Throwable;

/**
 * Reads .ini files
 */
final class IniReader implements FileReaderInterface
{
    public function read(string $file): array
    {
        try {
            return parse_ini_file($file, true, INI_SCANNER_TYPED);
        } catch (Throwable $e) {
            throw new ConfigurationFileException($file, $e->getCode(), $e, $e->getMessage());
        }
    }

    public function getExtensions(): array
    {
        return ['ini'];
    }
}
