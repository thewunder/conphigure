<?php
namespace Conphig\Test\FileReader;

use Conphig\FileReader\IniReader;
use Conphig\Test\BaseTestCase;

class IniReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new IniReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'inifile.ini'));
    }

    /**
     * @expectedException \Conphig\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new IniReader();
        $reader->read($this->getInvalidConfigDir() . 'inifile.ini');
    }
}
