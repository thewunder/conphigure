<?php

namespace Conphigure\Test\FileReader;

use Conphigure\FileReader\JsonReader;
use Conphigure\Test\BaseTestCase;

class JsonReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new JsonReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'jsonfile.json'));
    }

    /**
     * @expectedException \Conphigure\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new JsonReader();
        $reader->read($this->getInvalidConfigDir() . 'jsonfile.json');
    }
}
