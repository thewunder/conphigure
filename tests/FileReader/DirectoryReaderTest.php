<?php

namespace Conphigure\Test\FileReader;

use Conphigure\Conphigure;
use Conphigure\Exception\ConfigurationFileException;
use Conphigure\FileReader\PhpReader;
use Conphigure\FileReader\DirectoryReader;
use Conphigure\Test\BaseTestCase;

final class DirectoryReaderTest extends BaseTestCase
{
    public function testRead(): void
    {
        $config = $this->getMockConfig();
        $dirSource = new DirectoryReader($config);
        $configuration = $dirSource->read($this->getConfigDir());

        $testData = $this->getSimpleTestData();
        $this->assertArrayHasKey('simple', $configuration);
        $this->assertEquals('simple value', $configuration['simple']);
        $this->assertEquals($testData, $configuration['jsonfile']);
        $this->assertEquals($testData, $configuration['phpfile']);
        $this->assertEquals($testData, $configuration['yamlfile']);
        $this->assertArrayHasKey('subdir', $configuration);
        $this->assertEquals($testData, $configuration['subdir']['phpfile']);
        $this->assertArrayHasKey('subsubdir', $configuration['subdir']);
        $this->assertEquals($testData, $configuration['subdir']['subsubdir']['phpfile']);
    }

    public function testReadNoPrefix(): void
    {
        $config = $this->getMockConfig();
        $dirSource = new DirectoryReader($config, false);
        $configuration = $dirSource->read($this->getConfigDir());

        $testData = $this->getSimpleTestData();
        $this->assertEquals($testData, $configuration);
    }

    public function testReadNonDirectory(): void
    {
        $this->expectException(ConfigurationFileException::class);
        $this->expectExceptionMessageMatches('/^Error reading configuration file .+ must be a directory$/');
        $config = $this->getMockConfig();
        $dirSource = new DirectoryReader($config, false);
        $configuration = $dirSource->read($this->getConfigDir() . 'phpfile.php');

        $testData = $this->getSimpleTestData();
        $this->assertEquals($testData, $configuration);
    }

    protected function getMockConfig(): Conphigure
    {
        $config = $this->getMockBuilder(Conphigure::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFileReader'])
            ->getMock();

        $reader = $this->getMockBuilder(PhpReader::class)
            ->onlyMethods(['read'])
            ->getMock();
        $reader->method('read')->willReturn($this->getSimpleTestData());
        $config->method('getFileReader')->willReturn($reader);
        return $config;
    }
}
