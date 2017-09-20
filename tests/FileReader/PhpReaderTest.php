<?php
namespace Config\Test\FileReader;

use Config\FileReader\PhpReader;
use Config\Test\BaseTestCase;

class PhpReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new PhpReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'phpfile.php'));
    }

    /**
     * @expectedException \Config\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new PhpReader();
        $reader->read($this->getInvalidConfigDir() . 'phpfile.php');
    }
}