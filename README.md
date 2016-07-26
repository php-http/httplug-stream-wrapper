# Stream wrapper

[![Latest Version](https://img.shields.io/github/release/php-http/httplug-stream-wrapper.svg?style=flat-square)](https://github.com/php-http/httplug-stream-wrapper/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/php-http/httplug-stream-wrapper.svg?style=flat-square)](https://travis-ci.org/php-http/httplug-stream-wrapper)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/php-http/httplug-stream-wrapper.svg?style=flat-square)](https://scrutinizer-ci.com/g/php-http/httplug-stream-wrapper)
[![Quality Score](https://img.shields.io/scrutinizer/g/php-http/httplug-stream-wrapper.svg?style=flat-square)](https://scrutinizer-ci.com/g/php-http/httplug-stream-wrapper)
[![Total Downloads](https://img.shields.io/packagist/dt/php-http/httplug-stream-wrapper.svg?style=flat-square)](https://packagist.org/packages/php-http/httplug-stream-wrapper)

**Convert uses of fopen and file_get_contents to HTTPlug requests.**

## Disclaimer

**Warning 1**: There is an increased risk for problems because of how deeply the stream wrapper touches into the PHP core. Internally we use `stream_wrapper_unregister` and `stream_wrapper_register`.

**Warning 2**: Instead of using the stream wrapper you should should try to port libraries to use Httplug for a more robust solution.

## Install

Via Composer

``` bash
$ composer require php-http/httplug-stream-wrapper
```


## Documentation

```php
$httpClient = HttpClientDiscovery::find();
StreamWrapper::enable($httpClient);
```

Please see the [official documentation](http://docs.php-http.org/en/latest).


## Testing

Then the test suite:

``` bash
$ composer test
```


## Contributing

Please see our [contributing guide](http://docs.php-http.org/en/latest/development/contributing.html).


## Security

If you discover any security related issues, please contact us at [security@php-http.org](mailto:security@php-http.org).


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
