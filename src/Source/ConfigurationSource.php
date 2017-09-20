<?php

namespace Config\Source;

/**
 * Represents a source of configuration
 */
interface ConfigurationSource
{
    /**
     * @return array An array of all configuration data available from this source
     */
    public function load(): array;
}