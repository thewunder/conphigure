<?php
namespace Conphigure\Test\FileReader;

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

    /**
     * @expectedException \Conphigure\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new PhpReader();
        $reader->read($this->getInvalidConfigDir() . 'phpfile.php');
    }
}
