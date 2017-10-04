<?php

namespace Conphigure\Test\FileReader;

use Conphigure\FileReader\YamlReader;
use Conphigure\Test\BaseTestCase;
use Symfony\Component\Yaml\Parser;

class YamlReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new YamlReader(new Parser());

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'yamlfile.yml'));
    }

    /**
     * @expectedException \Conphigure\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new YamlReader(new Parser());
        $reader->read($this->getInvalidConfigDir() . 'yamlfile.yml');
    }
}
