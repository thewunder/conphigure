<?php

namespace Config\Test\Source;

use Config\ConfigurationManager;
use Config\FileReader\PhpReader;
use Config\Source\DirectorySource;
use Config\Test\BaseTestCase;

class DirectorySourceTest extends BaseTestCase
{
    public function testLoad()
    {
        $config = $this->getMockConfig();
        $dirSource = new DirectorySource($config, $this->getConfigDir());
        $configuration = $dirSource->load();

        $testData = $this->getSimpleTestData();
        $this->assertEquals($testData, $configuration['jsonfile']);
        $this->assertEquals($testData, $configuration['phpfile']);
        $this->assertEquals($testData, $configuration['yamlfile']);
    }

    public function testLoadNoPrefix()
    {
        $config = $this->getMockConfig();
        $dirSource = new DirectorySource($config, $this->getConfigDir(),false);
        $configuration = $dirSource->load();

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