# Conphigure

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

Conphigure is a framework agnostic library for reading and retrieving configuration.
If your application has outgrown a single configuration file this library will be a good choice.

It can read configuration files in the following formats:

- php
- yaml
- json
- xml
- ini
- dotenv

Conphigure can also read entire directories containing configuration files.

## Install

Via Composer

``` bash
$ composer require thewunder/conphigure
```

## Usage

If you have configuration in myfile.yml

``` yaml
smtp:
  host: smtp.mycompany.com
  port: 25

```

Read it in your php application like the following

``` php
$config = Conphigure::create();

//load configuration from a single file
$config->read('/directory/myfile.yml');

//get a value
$port = $config->get('smtp/port');

//add configuration from somewhere else (cache / database / etc)
$arrayFromSomewhere = [
     'database' => [
         'host' => 'localhost'
     ]
 ];
$config->addConfiguration($arrayFromSomewhere);

//you can also use it like an array
$host = $config['database']['host'];
$host = $config['database/host'];

//throws an exception if a value is missing
$value = $config->get('missing/key');

```

When reading a config directory Conphigure will (by default) organize the configuration in each file into a common root based
on the file path.

For example, a directory /directory/config/ with:

- system.yml
- email.yml
- logging.yml
- subdirectory/something.yml

``` php
//read the directory
$config->read('/directory/config/');

//get all configuration as an array
$all = $config->all();
var_export($all);
/* The result will be:
[
    'system' => ['...'], //contents of system.yml
    'email' => ['...'], //contents of email.yml
    'logging' => ['...'], //contents of logging.yml
    'subdirectory' => [
        'something' => ['...'], //contents of subdirectory/something.yml
    ]
];
*/

```

This allows you to keep things organized, and keep each file very flat.

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
[ico-downloads]: https://img.shields.io/packagist/dt/thewunder/conphigure.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/thewunder/conphigure
[link-coverage]: https://coveralls.io/github/thewunder/conphigure?branch=master
[link-downloads]: https://packagist.org/packages/thewunder/conphigure
[link-author]: https://github.com/thewunder
[link-contributors]: ../../contributors
