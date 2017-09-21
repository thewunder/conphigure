<?php

namespace Config\Test\FileReader;

use Config\ConfigurationManager;
use Config\FileReader\PhpReader;
use Config\FileReader\DirectoryReader;
use Config\Test\BaseTestCase;

class DirectoryReaderTest extends BaseTestCase
{
    public function testLoad()
    {
        $config = $this->getMockConfig();
        $dirSource = new DirectoryReader($config);
        $configuration = $dirSource->read($this->getConfigDir());

        $testData = $this->getSimpleTestData();
        $this->assertEquals($testData, $configuration['jsonfile']);
        $this->assertEquals($testData, $configuration['phpfile']);
        $this->assertEquals($testData, $configuration['yamlfile']);
        $this->assertArrayHasKey('subdir', $configuration);
        $this->assertEquals($testData, $configuration['subdir']['phpfile']);
        $this->assertArrayHasKey('subsubdir', $configuration['subdir']);
        $this->assertEquals($testData, $configuration['subdir']['subsubdir']['phpfile']);
    }

    public function testLoadNoPrefix()
    {
        $config = $this->getMockConfig();
        $dirSource = new DirectoryReader($config,false);
        $configuration = $dirSource->read($this->getConfigDir());

        $testData = $this->getSimpleTestData();
        $this->assertEquals($testData, $configuration);
    }

    protected function getMockConfig(): ConfigurationManager
    {
        $config = $this->getMockBuilder(ConfigurationManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFileReader'])
            ->getMock();

        $reader = $this->getMockBuilder(PhpReader::class)
            ->setMethods(['read'])
            ->getMock();
        $reader->method('read')->willReturn($this->getSimpleTestData());
        $config->method('getFileReader')->willReturn($reader);
        return $config;
    }
}