<?php

namespace Config\Test;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected function getConfigDir(): string
    {
        return realpath(__DIR__ . '/config') . '/';
    }
}