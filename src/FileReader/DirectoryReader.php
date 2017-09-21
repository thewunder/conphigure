<?php

namespace Config\FileReader;


use Config\Configuration;
use Config\Exception\ConfigurationFileException;

class DirectoryReader extends FileReader
{
    /**
     * @var Configuration
     */
    private $config;
    /**
     * @var bool
     */
    private $prefixWithFile;

    public function __construct(Configuration $config, bool $prefixWithFile = true)
    {
        $this->config = $config;
        $this->prefixWithFile = $prefixWithFile;
    }

    public function read(string $path): array
    {
        if (!is_dir($path)) {
            throw new ConfigurationFileException($path, 0, null, ' must be a directory');
        }

        $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
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

    function getExtensions(): array
    {
        return ['/'];
    }
}
