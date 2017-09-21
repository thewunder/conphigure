# Conphig

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-coverage]][link-coverage]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Conphig is a framework agnostic configuration framework for php 7+. 

It can read individual files and directories in the following formats:

- php
- yaml
- json
- ini

## Install

Via Composer

``` bash
$ composer require thewunder/conphig
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

//throws an exception if a value is missing
$value = $config->get('missing/key');

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

[ico-version]: https://img.shields.io/packagist/v/thewunder/conphig.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thewunder/conphig/master.svg?style=flat-square
[ico-coverage]: https://coveralls.io/repos/github/thewunder/conphig/badge.svg?branch=master
[ico-code-quality]: https://insight.sensiolabs.com/projects/a7c49441-93c2-4480-9902-3c428473073d/mini.png
[ico-downloads]: https://img.shields.io/packagist/dt/thewunder/conphig.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/thewunder/conphig
[link-travis]: https://travis-ci.org/thewunder/conphig
[link-coverage]: https://coveralls.io/github/thewunder/conphig?branch=master
[link-code-quality]: https://insight.sensiolabs.com/projects/a7c49441-93c2-4480-9902-3c428473073d
[link-downloads]: https://packagist.org/packages/thewunder/conphig
[link-author]: https://github.com/thewunder
[link-contributors]: ../../contributors
