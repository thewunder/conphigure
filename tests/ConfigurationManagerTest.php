<?php

namespace Config\Test;

use Config\ConfigurationManager;

class ConfigurationManagerTest extends BaseTestCase
{
    public function testHas()
    {
        $config = new ConfigurationManager([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertTrue($config->has('nested/key1'));
        $this->assertFalse($config->has('asdf'));
    }

    public function testGet()
    {
        $config = new ConfigurationManager([]);
        $config->addConfiguration($this->getSimpleTestData());
        $this->assertEquals('value1', $config->get('nested/key1'));
    }

    /**
     * @expectedException \Config\Exception\ConfigurationMissingException
     */
    public function testMissing()
    {
        $config = new ConfigurationManager([]);
        $config->addConfiguration($this->getSimpleTestData());
        $config->get('nested/asdf');
    }
}