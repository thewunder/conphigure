<?php

namespace Conphigure\Test\FileReader;

use Conphigure\Exception\ConfigurationFileException;
use Conphigure\FileReader\XmlReader;
use Conphigure\Test\BaseTestCase;

class XmlReaderTest extends BaseTestCase
{
    public function testRead()
    {
        $reader = new XmlReader();

        $fileConfig = $reader->read($this->getConfigDir() . 'xmlfile.xml');

        $this->assertEquals($this->getSimpleTestData(), $fileConfig);
    }

    public function testOther()
    {
        $reader = new XmlReader();

        $fileConfig = $reader->read($this->getConfigDir() . '../other/xmlfile.xml');

        $this->assertEquals(['attr1'=>'value1', 'attr2'=>'value2'], $fileConfig['withAttributes']);
        $this->assertEquals('value2', $fileConfig['duplicate']);
        $this->assertEquals(['attr'=>'attr', 'child1'=>'value1', 'child2'=>'value2'], $fileConfig['childrenWin']);
    }

    public function testInvalid()
    {
        $this->expectException(ConfigurationFileException::class);

        $reader = new XmlReader();
        $reader->read($this->getInvalidConfigDir() . 'xmlfile.xml');
    }
}
