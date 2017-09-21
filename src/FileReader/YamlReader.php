<?php

namespace Conphig\FileReader;

use Conphig\Exception\ConfigurationFileException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlReader extends FileReader
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct()
    {
        if (!class_exists('Symfony\\Component\\Yaml\\Parser')) {
            throw new ConfigurationFileException('Symfony yaml must be installed to read yaml files');
        }
        $this->parser = new Parser();
    }

    public function getExtensions(): array
    {
        return ['yml', 'yaml'];
    }

    public function read(string $file): array
    {
        $this->validateFile($file);
        try {
            return $this->parser->parse(file_get_contents($file));
        } catch (ParseException $e) {
            throw new ConfigurationFileException($file, $e->getCode(), $e, ' was not valid yaml');
        }
    }
}
