<?php
namespace Conphigure\Test\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Conphigure\FileReader\PhpReader;
use Conphigure\Test\BaseTestCase;

class PhpReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new PhpReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'phpfile.php'));
    }

    public function testInvalid()
    {
        $this->expectException(ConfigurationFileException::class);

        $reader = new PhpReader();
        $reader->read($this->getInvalidConfigDir() . 'phpfile.php');
    }
}
