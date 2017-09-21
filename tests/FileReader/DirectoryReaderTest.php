<?php

namespace Conphig\Test\FileReader;

use Conphig\Configuration;
use Conphig\FileReader\PhpReader;
use Conphig\FileReader\DirectoryReader;
use Conphig\Test\BaseTestCase;

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
        $dirSource = new DirectoryReader($config, false);
        $configuration = $dirSource->read($this->getConfigDir());

        $testData = $this->getSimpleTestData();
        $this->assertEquals($testData, $configuration);
    }

    protected function getMockConfig(): Configuration
    {
        $config = $this->getMockBuilder(Configuration::class)
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
