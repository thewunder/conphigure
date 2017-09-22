<?php

namespace Conphigure\Exception;

use Throwable;

/**
 * Thrown when a particular configuration value was not found
 */
class ConfigurationMissingException extends ConphigureException
{
    public function __construct($key = '', $code = 0, Throwable $previous = null)
    {
        $message = 'Configuration key ' . $key . ' was not found';
        parent::__construct($message, $code, $previous);
    }
}
