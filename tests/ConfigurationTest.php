<?php

namespace Conphig\Test;

use Conphig\Configuration;
use Conphig\Exception\ConfigurationFileException;
use Conphig\FileReader\DirectoryReader;
use Conphig\FileReader\JsonReader;
use Conphig\FileReader\PhpReader;
use Conphig\FileReader\YamlReader;

class ConfigurationTest extends BaseTestCase
{
    public function testGet()
    {
        $config = new Configuration([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertEquals('value1', $config->get('nested/key1'));
    }

    public function testGetWithDefault()
    {
        $config = new Configuration([]);
        $this->assertEquals(false, $config->get('nested/key1', false));
    }

    /**
     * @expectedException \Conphig\Exception\ConfigurationMissingException
     */
    public function testMissing()
    {
        $config = new Configuration([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->get('nested/asdf');
    }

    public function testOverwrite()
    {
        $config = new Configuration([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->addConfiguration(['nested'=>['key1'=>'new']]);
        $this->assertEquals('new', $config->get('nested/key1'));
    }

    public function testGetFileReader()
    {
        $config = new Configuration([new PhpReader()]);
        $reader = $config->getFileReader('dir/file.php');
        $this->assertInstanceOf(PhpReader::class, $reader);
    }

    public function testGetDirectoryReader()
    {
        $config = new Configuration([new PhpReader()]);
        $config->addFileReader(new DirectoryReader($config));
        $reader = $config->getFileReader($this->getConfigDir());
        $this->assertInstanceOf(DirectoryReader::class, $reader);
    }

    /**
     * @expectedException \Conphig\Exception\ConfigurationFileException
     */
    public function testGetMissingFileReader()
    {
        $config = new Configuration([new PhpReader()]);
        $config->getFileReader('dir/file.toml');
    }

    public function testCreate()
    {
        $config = Configuration::create();
        $this->assertInstanceOf(Configuration::class, $config);

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
        $config = Configuration::create([new PhpReader()]);
        $this->assertInstanceOf(Configuration::class, $config);

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
        $config = Configuration::create([new PhpReader()]);
        $config->read($this->getConfigDir() . 'phpfile.php');
        $this->assertEquals($this->getSimpleTestData(), $config->all());
    }

    public function testReadDirectory()
    {
        $config = Configuration::create();
        $config->read($this->getConfigDir());
        $testData = $this->getSimpleTestData();
        $this->assertEquals($testData, $config->get('phpfile'));
        $this->assertEquals($testData, $config->get('yamlfile'));
        $this->assertEquals($testData, $config->get('jsonfile'));
        $this->assertEquals($testData, $config->get('inifile'));
        $this->assertEquals($testData, $config->get('subdir/phpfile'));
        $this->assertEquals($testData, $config->get('subdir/subsubdir/phpfile'));
    }

    public function testReadDirectoryNoPrefix()
    {
        $config = Configuration::create([], '/', false);
        $config->read($this->getConfigDir());
        $this->assertEquals($this->getSimpleTestData(), $config->all());
    }
}
