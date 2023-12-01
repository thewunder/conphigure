<?php

namespace Conphigure\FileReader;

use Conphigure\Exception\ConfigurationFileException;

/**
 * Reads simple XML files.
 *
 * The root element is omitted. Multiple elements with the same name overwrite the previous value.
 * For elements with text values and children or attributes, the text value will be ignored.
 */
class XmlReader implements FileReaderInterface
{
    public function read(string $file): array
    {
        try {
            $document = simplexml_load_file($file);
            $config = [];
            foreach ($document as $element) {
                $this->readRecursive($element, $config);
            }
            $error = libxml_get_last_error();
            if ($error) {
                throw new ConfigurationFileException($file, $error->code, null, "$error->message on line $error->line");
            }
            return $config;
        } catch (\Throwable $e) {
            throw new ConfigurationFileException($file, $e->getCode(), $e, $e->getMessage());
        }
    }

    private function readRecursive(\SimpleXMLElement $element, array &$config)
    {
        if (!$element->count() && !$element->attributes()) {
            $config[$element->getName()] = (string) $element;
            return;
        }

        $childConfig = [];

        foreach ($element->children() as $child) {
            $this->readRecursive($child, $childConfig);
        }

        foreach ($element->attributes() as $attribute) {
            $this->readRecursive($attribute, $childConfig);
        }

        $config[$element->getName()] = $childConfig;
    }

    public function getExtensions(): array
    {
        return ['xml'];
    }
}
