<?php

namespace Conphig\Test\FileReader;

use Conphig\FileReader\JsonReader;
use Conphig\Test\BaseTestCase;

class JsonReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new JsonReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'jsonfile.json'));
    }

    /**
     * @expectedException \Conphig\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new JsonReader();
        $reader->read($this->getInvalidConfigDir() . 'jsonfile.json');
    }
}
