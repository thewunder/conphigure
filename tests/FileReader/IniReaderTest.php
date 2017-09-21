<?php
namespace Config\Test\FileReader;

use Config\FileReader\IniReader;
use Config\Test\BaseTestCase;

class IniReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new IniReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'inifile.ini'));
    }

    /**
     * @expectedException \Config\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new IniReader();
        $reader->read($this->getInvalidConfigDir() . 'inifile.ini');
    }
}
