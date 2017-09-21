<?php

namespace Conphig\Test\FileReader;

use Conphig\FileReader\YamlReader;
use Conphig\Test\BaseTestCase;

class YamlReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new YamlReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'yamlfile.yml'));
    }

    /**
     * @expectedException \Conphig\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new YamlReader();
        $reader->read($this->getInvalidConfigDir() . 'yamlfile.yml');
    }
}
