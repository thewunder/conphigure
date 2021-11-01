<?php

namespace Conphigure\Test\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Conphigure\FileReader\EnvReader;
use Conphigure\Test\BaseTestCase;

class EnvReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new EnvReader();

        $config = ['simple'=>'simple value'];

        $this->assertEquals($config, $reader->read($this->getConfigDir() . '.env'));
    }

    public function testInvalid()
    {
        $this->expectException(ConfigurationFileException::class);

        $reader = new EnvReader();
        $reader->read($this->getInvalidConfigDir() . '.env');
    }
}
