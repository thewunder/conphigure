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
        $directory = new \RecursiveDirectoryIterator($this->path, \FilesystemIterator::SKIP_DOTS);
        $configuration = [];
        $this->traverseDirectory($directory, $configuration);

        return $configuration;
    }

    protected function traverseDirectory(\RecursiveDirectoryIterator $directory, array &$configuration)
    {
        /** @var \SplFileInfo $file */
        foreach ($directory as $file) {
            if ($file->isFile()) {
                $this->readFile($file, $configuration);
            } else if($directory->hasChildren()) {
                if($this->prefixWithFile) {
                    $configuration[$directory->getFilename()] = [];
                    $this->traverseDirectory($directory->getChildren(), $configuration[$directory->getFilename()]);
                } else {
                    $this->traverseDirectory($directory->getChildren(), $configuration);
                }
            }
        }
    }

    protected function readFile(\SplFileInfo $file, array &$configuration)
    {
        $fileName = $file->getBasename();

        $reader = $this->config->getFileReader($fileName);
        $fileConfig = $reader->read($file);
        if ($this->prefixWithFile) {
            $noExtension = str_replace('.' . $file->getExtension(), '', $fileName);
            $configuration[$noExtension] = $fileConfig;
        } else {
            $configuration = array_merge($configuration, $fileConfig);
        }
    }
}
