# Conphigure

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-coverage]][link-coverage]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Conphigure is a framework agnostic configuration framework for php 7+.

It can read individual files and directories in the following formats:

- php
- yaml
- json
- ini

## Install

Via Composer

``` bash
$ composer require thewunder/conphigure
```

## Usage

``` php
$config = Configuration::create();

//load configuration from a single file or directory
$config->read('/my/config/dir/');

//add configuration from somewhere else (cache / database / etc)
$config->addConfiguration($myArray)

//get a value
$host = $config->get('database/host');

//can also use like an array
$host = $config['database']['host'];
$host = $config['database/host'];

//throws an exception if a value is missing
$value = $config->get('missing/key');

```

Given a directory with:

- system.yml
- email.yml
- logging.yml
- subdirectory/something.yml

By default Conphigure will prefix the values in each file with the file name.

``` php
//this will be the result
$config = [
    'system' => ['...'], //contents of system.yml
    'email' => ['...'], //contents of email.yml
    'logging' => ['...'], //contents of logging.yml
    'subdirectory' => [
        'something' => ['...'], //contents of subdirectory/something.yml
    ]
];


```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Michael O'Connell][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/thewunder/conphigure.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thewunder/conphigure/master.svg?style=flat-square
[ico-coverage]: https://coveralls.io/repos/github/thewunder/conphigure/badge.svg?branch=master
[ico-code-quality]: https://insight.sensiolabs.com/projects/a7c49441-93c2-4480-9902-3c428473073d/mini.png
[ico-downloads]: https://img.shields.io/packagist/dt/thewunder/conphigure.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/thewunder/conphigure
[link-travis]: https://travis-ci.org/thewunder/conphigure
[link-coverage]: https://coveralls.io/github/thewunder/conphigure?branch=master
[link-code-quality]: https://insight.sensiolabs.com/projects/a7c49441-93c2-4480-9902-3c428473073d
[link-downloads]: https://packagist.org/packages/thewunder/conphigure
[link-author]: https://github.com/thewunder
[link-contributors]: ../../contributors
