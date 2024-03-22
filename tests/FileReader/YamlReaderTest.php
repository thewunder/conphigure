<?php

namespace Conphigure\Test\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Conphigure\FileReader\YamlReader;
use Conphigure\Test\BaseTestCase;
use Symfony\Component\Yaml\Parser;

final class YamlReaderTest extends BaseTestCase
{
    public function testRead(): void
    {
        $reader = new YamlReader(new Parser());

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'yamlfile.yml'));
    }

    public function testInvalid(): void
    {
        $this->expectException(ConfigurationFileException::class);

        $reader = new YamlReader(new Parser());
        $reader->read($this->getInvalidConfigDir() . 'yamlfile.yml');
    }
}
