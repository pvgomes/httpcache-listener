# Dafiti HttpCache Response Listener
[![Build Status](https://img.shields.io/travis/dafiti/httpcache-listener/master.svg?style=flat-square)](https://travis-ci.org/dafiti/httpcache-listener)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/dafiti/httpcache-listener/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/dafiti/httpcache-listener/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/dafiti/httpcache-listener/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/dafiti/httpcache-listener/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/dafiti/httpcache-listener.svg?style=flat-square)](https://packagist.org/packages/dafiti/httpcache-listener)
[![Total Downloads](https://img.shields.io/packagist/dt/dafiti/httpcache-listener.svg?style=flat-square)](https://packagist.org/packages/dafiti/httpcache-listener)
[![License](https://img.shields.io/packagist/l/dafiti/httpcache-listener.svg?style=flat-square)](https://packagist.org/packages/dafiti/httpcache-listener)

Automatic set response headers (Etag and MaxAge) for Silex Apps

## Instalation
The package is available on [Packagist](http://packagist.org/packages/dafiti/httpcache-listener).
Autoloading is [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) compatible.
```json
{
    "require": {
        "dafiti/httpcache-listener": "dev-master"
    }
}
```

## Usage

#### Basic
```php
use Dafiti\Silex\Listener;
use Silex\Application;
use Pimple;

$app = new Application();

// You can use [dafiti/config-service-provider](https://github.com/dafiti/config-service-provider) to manage the config files
$app['config'] = new Pimple();
$app['config']['http_cache'] = [
    'enabled' => true,
    'etag'    => true,
    'max_age' => 100
];

$app['dispatcher']->addSubscriber(new Listener\HttpCache($app['config']));
```

## License

MIT License
