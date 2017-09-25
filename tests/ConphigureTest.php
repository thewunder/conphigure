<?php

namespace Conphigure\Test;

use Conphigure\Conphigure;
use Conphigure\Exception\ConfigurationFileException;
use Conphigure\FileReader\DirectoryReader;
use Conphigure\FileReader\JsonReader;
use Conphigure\FileReader\PhpReader;
use Conphigure\FileReader\YamlReader;

class ConphigureTest extends BaseTestCase
{
    public function testGet()
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertEquals('value1', $config->get('nested/key1'));
    }

    public function testExtraSeparators()
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertEquals('value1', $config->get('/nested//key1/'));
    }

    public function testGetWithDefault()
    {
        $config = new Conphigure([]);
        $this->assertEquals(false, $config->get('nested/key1', false));
    }

    /**
     * @expectedException \Conphigure\Exception\ConfigurationMissingException
     */
    public function testMissing()
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->get('nested/asdf');
    }

    public function testHas()
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertTrue($config->has('nested/key1'));
        $this->assertFalse($config->has('nested/missing'));
    }

    public function testSet()
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->set('nested/key1', 'new');
        $config->set('newnested/newkey', 'new');
        $this->assertEquals('new', $config->get('nested/key1'));
        $this->assertEquals('new', $config->get('newnested/newkey'));
    }

    /**
     * @expectedException \Conphigure\Exception\ConphigureException
     * @expectedExceptionMessageRegExp /^Refusing to overwrite existing non-array value at/
     */
    public function testSetOverwriteException()
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->set('simple/key1', 'new');
    }

    public function testRemove()
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->remove('nested/key1');
        $this->assertFalse($config->has('nested/key1'));
    }

    public function testOverwrite()
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->addConfiguration(['nested'=>['key1'=>'new']]);
        $this->assertEquals('new', $config->get('nested/key1'));
    }

    public function testGetFileReader()
    {
        $config = new Conphigure([new PhpReader()]);
        $reader = $config->getFileReader('dir/file.php');
        $this->assertInstanceOf(PhpReader::class, $reader);
    }

    public function testGetDirectoryReader()
    {
        $config = new Conphigure([new PhpReader()]);
        $config->addFileReader(new DirectoryReader($config));
        $reader = $config->getFileReader($this->getConfigDir());
        $this->assertInstanceOf(DirectoryReader::class, $reader);
    }

    /**
     * @expectedException \Conphigure\Exception\ConfigurationFileException
     */
    public function testGetMissingFileReader()
    {
        $config = new Conphigure([new PhpReader()]);
        $config->getFileReader('dir/file.toml');
    }

    public function testCreate()
    {
        $config = Conphigure::create();
        $this->assertInstanceOf(Conphigure::class, $config);

        //test that all readers have been added
        $reader = $config->getFileReader('dir/file.php');
        $this->assertInstanceOf(PhpReader::class, $reader);
        $reader = $config->getFileReader('dir/file.yaml');
        $this->assertInstanceOf(YamlReader::class, $reader);
        $reader = $config->getFileReader('dir/file.json');
        $this->assertInstanceOf(JsonReader::class, $reader);
        $reader = $config->getFileReader($this->getConfigDir());
        $this->assertInstanceOf(DirectoryReader::class, $reader);
    }

    public function testCreateWithReaders()
    {
        $config = Conphigure::create([new PhpReader()]);
        $this->assertInstanceOf(Conphigure::class, $config);

        //test that all readers have been added
        $reader = $config->getFileReader('dir/file.php');
        $this->assertInstanceOf(PhpReader::class, $reader);

        try {
            $config->getFileReader('dir/file.yaml');
            $this->fail('Expected ConfigurationFileException');
        } catch (ConfigurationFileException $e) {
            $this->addToAssertionCount(1);
        }
    }

    public function testReadFile()
    {
        $config = Conphigure::create([new PhpReader()]);
        $config->read($this->getConfigDir() . 'phpfile.php');
        $this->assertEquals($this->getSimpleTestData(), $config->all());
    }

    public function testReadFileWithPrefix()
    {
        $config = Conphigure::create([new PhpReader()]);
        $config->read($this->getConfigDir() . 'phpfile.php', 'prefix/sub');
        $this->assertEquals($this->getSimpleTestData(), $config->get('prefix/sub'));
    }

    /**
     * @expectedException \Conphigure\Exception\ConfigurationFileException
     * @expectedExceptionMessageRegExp  /^Error reading configuration file .+ does not exist$/
     */
    public function testReadMissingFile()
    {
        $config = Conphigure::create([new PhpReader()]);
        $config->read($this->getConfigDir() . 'missing.php');
    }

    /**
     * @expectedException \Conphigure\Exception\ConfigurationFileException
     * @expectedExceptionMessageRegExp  /^Error reading configuration file .+ is not readable$/
     */
    public function testReadUnreadable()
    {
        if (file_exists('/root')) {
            $config = Conphigure::create([new PhpReader()]);
            $config->read('/root');
        } else {
            $this->markTestSkipped('/root does not exist');
        }
    }

    public function testReadDirectory()
    {
        $config = Conphigure::create();
        $config->read($this->getConfigDir());
        $testData = $this->getSimpleTestData();
        $this->assertEquals($testData, $config->get('phpfile'));
        $this->assertEquals($testData, $config->get('yamlfile'));
        $this->assertEquals($testData, $config->get('jsonfile'));
        $this->assertEquals($testData, $config->get('inifile'));
        $this->assertEquals($testData, $config->get('subdir/phpfile'));
        $this->assertEquals($testData, $config->get('subdir/subsubdir/phpfile'));
    }

    public function testReadDirectoryNoPrefix()
    {
        $config = Conphigure::create([], '/', false);
        $config->read($this->getConfigDir());
        $this->assertEquals($this->getSimpleTestData(), $config->all());
    }

    public function testArrayAccessGet()
    {
        $config = Conphigure::create([new PhpReader()]);

        $config->addConfiguration($this->getSimpleTestData());
        $this->assertEquals('value1', $config['nested/key1']);
    }

    public function testArrayAccessIsset()
    {
        $config = Conphigure::create([new PhpReader()]);

        $config->addConfiguration($this->getSimpleTestData());
        $this->assertTrue(isset($config['nested/key1']));
        $this->assertFalse(isset($config['nested/missing']));
    }

    public function testArrayAccessSet()
    {
        $config = Conphigure::create([new PhpReader()]);

        $config['nested/key1'] = 'value1';
        $this->assertEquals('value1', $config->get('nested/key1'));
    }

    public function testArrayAccessUnset()
    {
        $config = Conphigure::create([new PhpReader()]);
        $config->addConfiguration($this->getSimpleTestData());
        unset($config['nested/key1']);
        $this->assertFalse($config->has('nested/key1'));
    }
}
