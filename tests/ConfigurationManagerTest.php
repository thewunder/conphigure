<?php

namespace Config\Test;

use Config\ConfigurationManager;
use Config\FileReader\PhpReader;

class ConfigurationManagerTest extends BaseTestCase
{
    public function testHas()
    {
        $config = new ConfigurationManager([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertTrue($config->has('nested/key1'));
        $this->assertFalse($config->has('asdf'));
    }

    public function testGet()
    {
        $config = new ConfigurationManager([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertEquals('value1', $config->get('nested/key1'));
    }

    /**
     * @expectedException \Config\Exception\ConfigurationMissingException
     */
    public function testMissing()
    {
        $config = new ConfigurationManager([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->get('nested/asdf');
    }

    public function testGetFileReader()
    {
        $config = new ConfigurationManager([new PhpReader()]);
        $reader = $config->getFileReader('dir/file.php');
        $this->assertInstanceOf(PhpReader::class, $reader);
    }

    /**
     * @expectedException \Config\Exception\ConfigurationFileException
     */
    public function testGetMissingFileReader()
    {
        $config = new ConfigurationManager([new PhpReader()]);
        $reader = $config->getFileReader('dir/file.toml');
    }
}