<?php

namespace Conphigure\Test\FileReader;

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

    /**
     * @expectedException \Conphigure\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new EnvReader();
        $reader->read($this->getInvalidConfigDir() . '.env');
    }
}
