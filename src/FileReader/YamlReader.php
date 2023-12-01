<?php

namespace Conphigure\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * Reads YAML files using Symfony's YAML parser
 */
final class YamlReader implements FileReaderInterface
{

    public function __construct(private readonly Parser $parser)
    {
    }

    public function getExtensions(): array
    {
        return ['yml', 'yaml'];
    }

    public function read(string $file): array
    {
        try {
            return $this->parser->parse(file_get_contents($file));
        } catch (ParseException $e) {
            throw new ConfigurationFileException($file, $e->getCode(), $e, ' was not valid yaml');
        }
    }
}
