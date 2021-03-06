<?php

namespace Conphigure\FileReader;

/**
 * Reads configuration data from a file
 */
interface FileReaderInterface
{
    /**
     * Returns an array of extensions this class can read
     *
     * @return array
     */
    public function getExtensions(): array;

    /**
     * Reads the file and returns the data contained therein
     *
     * @param string $file Full path to file
     * @return array
     */
    public function read(string $file): array;
}
