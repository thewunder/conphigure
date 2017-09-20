<?php

namespace Config\Test\FileReader;

use Config\FileReader\YamlReader;
use Config\Test\BaseTestCase;

class YamlReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new YamlReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'yamlfile.yml'));
    }

    /**
     * @expectedException \Config\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new YamlReader();
        $reader->read($this->getInvalidConfigDir() . 'yamlfile.yml');
    }
}