<?php
namespace Conphig\Test\FileReader;

use Conphig\FileReader\PhpReader;
use Conphig\Test\BaseTestCase;

class PhpReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new PhpReader();

        $config = $this->getSimpleTestData();

        $this->assertEquals($config, $reader->read($this->getConfigDir() . 'phpfile.php'));
    }

    /**
     * @expectedException \Conphig\Exception\ConfigurationFileException
     */
    public function testInvalid()
    {
        $reader = new PhpReader();
        $reader->read($this->getInvalidConfigDir() . 'phpfile.php');
    }
}
