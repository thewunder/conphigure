# Changelog

## 3.0

### New Features
- Require PHP 8.1
- Upgrade PHPUnit
- Support Symfony 6.x
- Support phpdotenv 4.x and 5.x

### Backward Compatibility Breaks
- Drop support for PHP 7.4 and 8.0
- Drop support for Symfony < 5
- Drop support for phpdotenv < 4.2
- Most classes made final

## 2.2

### New Features
- Add support for PHP 8.0
- Upgrade PHPUnit

### Backward Compatibility Breaks
- Drop support for PHP 7.1-7.3

## 2.0

### New Features
- Add ability to read XML files
- Allow Symfony YAML 5.x
- Tested in PHP 7.3 AND 7.4

### Backward Compatibility Breaks
- Drop support for PHP 7.0

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
