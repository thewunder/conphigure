<?php

namespace Conphigure\Test\FileReader;

use Conphigure\Exception\ConfigurationFileException;
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

    public function testInvalid()
    {
        $this->expectException(ConfigurationFileException::class);

        $reader = new JsonReader();
        $reader->read($this->getInvalidConfigDir() . 'jsonfile.json');
    }
}
