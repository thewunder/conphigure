<?php
namespace Conphigure\Test\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Conphigure\FileReader\PhpReader;
use Conphigure\Test\BaseTestCase;

final class PhpReaderTest extends BaseTestCase
{
    public function testRead(): void
    {
        $reader = new PhpReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'phpfile.php'));
    }

    public function testInvalid(): void
    {
        $this->expectException(ConfigurationFileException::class);

        $reader = new PhpReader();
        $reader->read($this->getInvalidConfigDir() . 'phpfile.php');
    }
}
