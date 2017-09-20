<?php

namespace Config\Test\FileReader;

use Config\FileReader\JsonReader;
use Config\Test\BaseTestCase;

class JsonReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new JsonReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'jsonfile.json'));
    }

    /**
     * @expectedException \Config\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new JsonReader();
        $reader->read($this->getInvalidConfigDir() . 'jsonfile.json');
    }
}