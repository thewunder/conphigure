<?php

namespace Conphigure\Test\FileReader;

use Conphigure\FileReader\YamlReader;
use Conphigure\Test\BaseTestCase;

class YamlReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new YamlReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'yamlfile.yml'));
    }

    /**
     * @expectedException \Conphigure\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new YamlReader();
        $reader->read($this->getInvalidConfigDir() . 'yamlfile.yml');
    }
}
