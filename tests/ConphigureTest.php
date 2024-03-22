<?php

namespace Conphigure\Test;

use Conphigure\Conphigure;
use Conphigure\Exception\ConfigurationFileException;
use Conphigure\Exception\ConfigurationMissingException;
use Conphigure\Exception\ConphigureException;
use Conphigure\FileReader\DirectoryReader;
use Conphigure\FileReader\EnvReader;
use Conphigure\FileReader\JsonReader;
use Conphigure\FileReader\PhpReader;
use Conphigure\FileReader\YamlReader;

final class ConphigureTest extends BaseTestCase
{
    public function testGet(): void
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertEquals('value1', $config->get('nested/key1'));
    }

    public function testExtraSeparators(): void
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertEquals('value1', $config->get('/nested//key1/'));
    }

    public function testGetWithDefault(): void
    {
        $config = new Conphigure([]);
        $this->assertEquals(false, $config->get('nested/key1', false));
    }

    public function testMissing(): void
    {
        $this->expectException(ConfigurationMissingException::class);

        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->get('nested/asdf');
    }

    public function testHas(): void
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertTrue($config->has('nested/key1'));
        $this->assertFalse($config->has('nested/missing'));
    }

    public function testSet(): void
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->set('nested/key1', 'new');
        $config->set('newnested/newkey', 'new');
        $this->assertEquals('new', $config->get('nested/key1'));
        $this->assertEquals('new', $config->get('newnested/newkey'));
    }

    public function testSetOverwriteException(): void
    {
        $this->expectException(ConphigureException::class);
        $this->expectExceptionMessageMatches('/^Refusing to overwrite existing non-array value at/');

        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->set('simple/key1', 'new');
    }

    public function testRemove(): void
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->remove('nested/key1');
        $this->assertFalse($config->has('nested/key1'));
    }

    public function testOverwrite(): void
    {
        $config = new Conphigure([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->addConfiguration(['nested'=>['key1'=>'new']]);
        $this->assertEquals('new', $config->get('nested/key1'));
    }

    public function testGetFileReader(): void
    {
        $config = new Conphigure([new PhpReader()]);
        $reader = $config->getFileReader('dir/file.php');
        $this->assertInstanceOf(PhpReader::class, $reader);
    }

    public function testGetDirectoryReader(): void
    {
        $config = new Conphigure([new PhpReader()]);
        $config->addFileReader(new DirectoryReader($config));
        $reader = $config->getFileReader($this->getConfigDir());
        $this->assertInstanceOf(DirectoryReader::class, $reader);
    }

    public function testGetMissingFileReader(): void
    {
        $this->expectException(ConfigurationFileException::class);

        $config = new Conphigure([new PhpReader()]);
        $config->getFileReader('dir/file.toml');
    }

    public function testCreate(): void
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
        $reader = $config->getFileReader('dir/file.env');
        $this->assertInstanceOf(EnvReader::class, $reader);
        $reader = $config->getFileReader($this->getConfigDir());
        $this->assertInstanceOf(DirectoryReader::class, $reader);
    }

    public function testCreateWithReaders(): void
    {
        $config = Conphigure::create([new PhpReader()]);
        $this->assertInstanceOf(Conphigure::class, $config);

        //test that all readers have been added
        $reader = $config->getFileReader('dir/file.php');
        $this->assertInstanceOf(PhpReader::class, $reader);

        try {
            $config->getFileReader('dir/file.yaml');
            $this->fail('Expected ConfigurationFileException');
        } catch (ConfigurationFileException) {
            $this->addToAssertionCount(1);
        }
    }

    public function testReadFile(): void
    {
        $config = Conphigure::create([new PhpReader()]);
        $return = $config->read($this->getConfigDir() . 'phpfile.php');
        $this->assertEquals($this->getSimpleTestData(), $config->all());
        $this->assertEquals($this->getSimpleTestData(), $return);
    }

    public function testReadFileWithPrefix(): void
    {
        $config = Conphigure::create([new PhpReader()]);
        $config->read($this->getConfigDir() . 'phpfile.php', 'prefix/sub');
        $this->assertEquals($this->getSimpleTestData(), $config->get('prefix/sub'));
    }

    public function testReadMissingFile(): void
    {
        $this->expectException(ConfigurationFileException::class);
        $this->expectExceptionMessageMatches('/^Error reading configuration file .+ does not exist$/');

        $config = Conphigure::create([new PhpReader()]);
        $config->read($this->getConfigDir() . 'missing.php');
    }

    public function testReadUnreadable(): void
    {
        $this->expectException(ConfigurationFileException::class);
        $this->expectExceptionMessageMatches('/^Error reading configuration file .+ is not readable$/');


        if (file_exists('/root')) {
            $config = Conphigure::create([new PhpReader()]);
            $config->read('/root');
        } else {
            $this->markTestSkipped('/root does not exist');
        }
    }

    public function testReadDirectory(): void
    {
        $config = Conphigure::create();
        $config->read($this->getConfigDir());
        $testData = $this->getSimpleTestData();
        $this->assertEquals($testData, $config->get('phpfile'));
        $this->assertEquals($testData, $config->get('yamlfile'));
        $this->assertEquals($testData, $config->get('jsonfile'));
        $this->assertEquals($testData, $config->get('inifile'));
        $this->assertEquals($testData, $config->get('xmlfile'));
        $this->assertEquals($testData, $config->get('subdir/phpfile'));
        $this->assertEquals($testData, $config->get('subdir/subsubdir/phpfile'));
    }

    public function testReadDirectoryNoPrefix(): void
    {
        $config = Conphigure::create([], '/', false);
        $config->read($this->getConfigDir());
        $this->assertEquals($this->getSimpleTestData(), $config->all());
    }

    public function testArrayAccessGet(): void
    {
        $config = Conphigure::create([new PhpReader()]);

        $config->addConfiguration($this->getSimpleTestData());
        $this->assertEquals('value1', $config['nested/key1']);
    }

    public function testArrayAccessIsset(): void
    {
        $config = Conphigure::create([new PhpReader()]);

        $config->addConfiguration($this->getSimpleTestData());
        $this->assertTrue(isset($config['nested/key1']));
        $this->assertFalse(isset($config['nested/missing']));
    }

    public function testArrayAccessSet(): void
    {
        $config = Conphigure::create([new PhpReader()]);

        $config['nested/key1'] = 'value1';
        $this->assertEquals('value1', $config->get('nested/key1'));
    }

    public function testArrayAccessUnset(): void
    {
        $config = Conphigure::create([new PhpReader()]);
        $config->addConfiguration($this->getSimpleTestData());
        unset($config['nested/key1']);
        $this->assertFalse($config->has('nested/key1'));
    }
}
