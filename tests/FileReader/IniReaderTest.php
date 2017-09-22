<?php
namespace Conphigure\Test\FileReader;

use Conphigure\FileReader\IniReader;
use Conphigure\Test\BaseTestCase;

class IniReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new IniReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'inifile.ini'));
    }

    /**
     * @expectedException \Conphigure\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new IniReader();
        $reader->read($this->getInvalidConfigDir() . 'inifile.ini');
    }
}
