<?php

namespace Config\Test;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected function getConfigDir(): string
    {
        return realpath(__DIR__ . '/config') . '/';
    }

    protected function getInvalidConfigDir(): string
    {
        return realpath(__DIR__ . '/invalid') . '/';
    }

    protected function getSimpleTestData(): array
    {
        return [
            'simple' => 'simple value',
            'nested' => [
                'key1'  => 'value1',
                'key2'  => 'value2'
            ]
        ];
    }
}