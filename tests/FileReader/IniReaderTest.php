<?php
namespace Conphigure\Test\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Conphigure\FileReader\IniReader;
use Conphigure\Test\BaseTestCase;

final class IniReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new IniReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'inifile.ini'));
    }

    public function testInvalid()
    {
        $this->expectException(ConfigurationFileException::class);

        $reader = new IniReader();
        $reader->read($this->getInvalidConfigDir() . 'inifile.ini');
    }
}
