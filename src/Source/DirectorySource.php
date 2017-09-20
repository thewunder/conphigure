<?php

namespace Config\Source;


use Config\ConfigurationManager;
use Config\Exception\ConfigurationFileException;

class DirectorySource implements ConfigurationSource
{
    /**
     * @var ConfigurationManager
     */
    private $config;
    /**
     * @var string
     */
    private $path;
    /**
     * @var bool
     */
    private $prefixWithFile;

    public function __construct(ConfigurationManager $config, string $path, bool $prefixWithFile = true)
    {
        if (!is_dir($path)) {
            throw new ConfigurationFileException($path, 0, null, ' must be a directory');
        }

        $this->config = $config;
        $this->path = $path;
        $this->prefixWithFile = $prefixWithFile;
    }

    public function load(): array
    {
        $iterator = new \RecursiveDirectoryIterator($this->path, \FilesystemIterator::SKIP_DOTS);
        $configuration = [];
        /** @var \SplFileInfo $value */
        foreach ($iterator as $value) {
            $fileName = $value->getBasename();
            $reader = $this->config->getFileReader($fileName);
            $fileConfig = $reader->read($value);
            if ($this->prefixWithFile) {
                $noExtension = str_replace('.'.$value->getExtension(), '', $fileName);
                $configuration[$noExtension] = $fileConfig;
            } else {
                $configuration = array_merge($configuration, $fileConfig);
            }
        }

        return $configuration;
    }
}
