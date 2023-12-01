<?php

namespace Conphigure\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Dotenv\Dotenv;

/**
 * Reads from .env files, unlike reading directly with the dotenv library, it will not set $_ENV or $_SERVER globals
 */
class EnvReader implements FileReaderInterface
{
    public function getExtensions(): array
    {
        return ['env'];
    }

    public function read(string $file): array
    {
        try {
            $content = file_get_contents($file);
            return Dotenv::parse($content);
        } catch (\Throwable $e) {
            throw new ConfigurationFileException($file, $e->getCode(), $e, $e->getMessage());
        }
    }
}
