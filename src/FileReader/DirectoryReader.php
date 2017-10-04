<?php

namespace Conphigure\FileReader;

use Conphigure\Conphigure;
use Conphigure\Exception\ConfigurationFileException;

class DirectoryReader implements FileReaderInterface
{
    /**
     * @var Conphigure
     */
    private $config;
    /**
     * @var bool
     */
    private $prefixWithFile;

    /**
     * @param Conphigure $config
     * @param bool $prefixWithFile Set contents of each file with a prefix based on the file name
     */
    public function __construct(Conphigure $config, bool $prefixWithFile = true)
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
            } elseif ($directory->hasChildren()) {
                if ($this->prefixWithFile) {
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
            if($noExtension) {
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
