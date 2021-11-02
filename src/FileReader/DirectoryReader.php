<?php

namespace Conphigure\FileReader;

use Conphigure\Conphigure;
use Conphigure\Exception\ConfigurationFileException;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use SplFileInfo;

/**
 * Reads recursively reads all configuration files in a directory and combines them into a single array
 */
class DirectoryReader implements FileReaderInterface
{
    private Conphigure $config;
    private bool $prefixWithFile;

    /**
     * @param Conphigure $config
     * @param bool $prefixWithFile Set contents of each file with a prefix based on the file name
     */
    public function __construct(Conphigure $config, bool $prefixWithFile = true)
    {
        $this->config = $config;
        $this->prefixWithFile = $prefixWithFile;
    }

    public function read(string $file): array
    {
        if (!is_dir($file)) {
            throw new ConfigurationFileException($file, 0, null, ' must be a directory');
        }

        $directory = new RecursiveDirectoryIterator($file, FilesystemIterator::SKIP_DOTS);
        $configuration = [];
        $this->traverseDirectory($directory, $configuration);

        return $configuration;
    }

    protected function traverseDirectory(RecursiveDirectoryIterator $directory, array &$configuration)
    {
        /** @var SplFileInfo $file */
        foreach ($directory as $file) {
            if ($file->isFile()) {
                $this->readFile($file, $configuration);
            } elseif ($directory->hasChildren()) {
                /** @var RecursiveDirectoryIterator $iterator */
                $iterator = $directory->getChildren();
                if ($this->prefixWithFile) {
                    $configuration[$directory->getFilename()] = [];
                    $this->traverseDirectory($iterator, $configuration[$directory->getFilename()]);
                } else {
                    $this->traverseDirectory($iterator, $configuration);
                }
            }
        }
    }

    protected function readFile(SplFileInfo $file, array &$configuration)
    {
        $fileName = $file->getBasename();

        $reader = $this->config->getFileReader($fileName);
        $fileConfig = $reader->read($file);
        if ($this->prefixWithFile) {
            $noExtension = str_replace('.' . $file->getExtension(), '', $fileName);
            if ($noExtension) {
                $configuration[$noExtension] = $fileConfig;
            } else {
                $configuration = array_merge($configuration, $fileConfig);
            }
        } else {
            $configuration = array_merge($configuration, $fileConfig);
        }
    }

    public function getExtensions(): array
    {
        return ['/'];
    }
}
