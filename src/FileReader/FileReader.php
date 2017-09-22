<?php

namespace Conphigure\FileReader;

/**
 * Reads configuration data from an individual file
 */
abstract class FileReader
{
    /**
     * Returns an array of extensions this class can read
     *
     * @return array
     */
    abstract public function getExtensions(): array;

    /**
     * Reads the file and returns the data contained there in
     *
     * @param string $file Full path to file
     * @return array
     */
    abstract public function read(string $file): array;
}
