# Changelog

## 1.1

### New Features
- Allow Symfony YAML 4.x
- Tested in PHP 7.2

## 1.0

### New Features
- Added .env file support
- Return configuration from file in read()
- More phpdoc

### Fixes
- Don't use an empty file name as a prefix when reading a directory

### Backward Compatibility Breaks
- Pass in Yaml parser to YamlReader as a constructor argument

## 0.9.5

### New Features
- Made FileReader an interface
- More phpdoc

### 0.9.4

### New Features
- Renamed to Conphigure
- Additional and improved documentation

## 0.9.3

### New Features

- Add optional prefix when reading a file

## 0.9.2

### Fixes

- Allow leading and trailing separators

## 0.9.1

### New Features

- Set and Remove methods
- Configuration now implements ArrayAccess

## 0.9.0

Initial Release
