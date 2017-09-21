<?php

namespace Conphig\Exception;

use Throwable;

/**
 * Thrown when there is an error reading a configuration file
 */
class ConfigurationFileException extends ConfigException
{
    public function __construct($file = '', $code = 0, Throwable $previous = null, $reason = '')
    {
        $message = 'Error reading configuration file ' . $file . $reason;
        parent::__construct($message, $code, $previous);
    }
}
