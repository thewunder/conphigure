<?php

namespace Conphigure\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * Reads YAML files using Symfony's YAML parser
 */
class YamlReader implements FileReaderInterface
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
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
