<?php

namespace Config\Test;

use Config\ConfigurationManager;
use Config\Exception\ConfigurationFileException;
use Config\FileReader\DirectoryReader;
use Config\FileReader\JsonReader;
use Config\FileReader\PhpReader;
use Config\FileReader\YamlReader;

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

    public function testGetDirectoryReader()
    {
        $config = new ConfigurationManager([new PhpReader()]);
        $config->addFileReader(new DirectoryReader($config));
        $reader = $config->getFileReader($this->getConfigDir());
        $this->assertInstanceOf(DirectoryReader::class, $reader);
    }

    /**
     * @expectedException \Config\Exception\ConfigurationFileException
     */
    public function testGetMissingFileReader()
    {
        $config = new ConfigurationManager([new PhpReader()]);
        $config->getFileReader('dir/file.toml');
    }

    public function testCreate()
    {
        $config = ConfigurationManager::create();
        $this->assertInstanceOf(ConfigurationManager::class, $config);

        //test that all readers have been added
        $reader = $config->getFileReader('dir/file.php');
        $this->assertInstanceOf(PhpReader::class, $reader);
        $reader = $config->getFileReader('dir/file.yaml');
        $this->assertInstanceOf(YamlReader::class, $reader);
        $reader = $config->getFileReader('dir/file.json');
        $this->assertInstanceOf(JsonReader::class, $reader);
        $reader = $config->getFileReader($this->getConfigDir());
        $this->assertInstanceOf(DirectoryReader::class, $reader);
    }

    public function testCreateWithReaders()
    {
        $config = ConfigurationManager::create([new PhpReader()]);
        $this->assertInstanceOf(ConfigurationManager::class, $config);

        //test that all readers have been added
        $reader = $config->getFileReader('dir/file.php');
        $this->assertInstanceOf(PhpReader::class, $reader);

        try {
            $config->getFileReader('dir/file.yaml');
            $this->fail('Expected ConfigurationFileException');
        } catch (ConfigurationFileException $e) {
            $this->addToAssertionCount(1);
        }
    }

    public function testReadFile()
    {
        $config = ConfigurationManager::create([new PhpReader()]);
        $config->read($this->getConfigDir() . 'phpfile.php');
        $this->assertEquals($this->getSimpleTestData(), $config->all());
    }

    public function testReadDirectory()
    {
        $config = ConfigurationManager::create();
        $config->read($this->getConfigDir());
        $this->assertEquals($this->getSimpleTestData(), $config->get('phpfile'));
        $this->assertEquals($this->getSimpleTestData(), $config->get('yamlfile'));
        $this->assertEquals($this->getSimpleTestData(), $config->get('jsonfile'));
        $this->assertEquals($this->getSimpleTestData(), $config->get('subdir/phpfile'));
        $this->assertEquals($this->getSimpleTestData(), $config->get('subdir/subsubdir/phpfile'));
    }
}
