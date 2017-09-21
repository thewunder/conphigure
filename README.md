# Conphig

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Conphig is a multi-source framework agnostic configuration framework. 

It can read individual files and directories with several types of file including:

- php files
- yaml files
- json files
- ini files

## Install

Via Composer

``` bash
$ composer require thewunder/conphig
```

## Usage

``` php
$c = new Config();
echo $skeleton->echoPhrase('Hello, League!');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email wundbread@gmail.com instead of using the issue tracker.

## Credits

- [Michael O'Connell][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/thewunder/conphig.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thewunder/conphig/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thewunder/conphig.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thewunder/conphig.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/thewunder/conphig.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/thewunder/conphig
[link-travis]: https://travis-ci.org/thewunder/conphig
[link-scrutinizer]: https://scrutinizer-ci.com/g/thewunder/conphig/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thewunder/conphig
[link-downloads]: https://packagist.org/packages/thewunder/conphig
[link-author]: https://github.com/thewunder
[link-contributors]: ../../contributors
