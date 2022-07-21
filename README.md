[![Latest Stable Version](https://img.shields.io/packagist/v/webclient/ext-retry.svg?style=flat-square)](https://packagist.org/packages/webclient/ext-retry)
[![Total Downloads](https://img.shields.io/packagist/dt/webclient/ext-retry.svg?style=flat-square)](https://packagist.org/packages/webclient/ext-retry/stats)
[![License](https://img.shields.io/packagist/l/webclient/ext-retry.svg?style=flat-square)](https://github.com/phpwebclient/ext-retry/blob/master/LICENSE)
[![PHP](https://img.shields.io/packagist/php-v/webclient/ext-retry.svg?style=flat-square)](https://php.net)

# webclient/ext-retry

Retry extension for PSR-18 HTTP client. 

# Install

Install this package and your favorite [psr-18 implementation](https://packagist.org/providers/psr/http-client-implementation).

```bash
composer require webclient/ext-retry:^1.0
```

# Using

```php
<?php

use Webclient\Extension\Retry\RetryClientDecorator;
use \Webclient\Extension\Retry\Timer\Timer;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/** 
 * @var ClientInterface $client Your PSR-18 HTTP Client
 * @var Timer $timer Timer implements
 * @var int $maxAttempts Max attempts
 */
$http = new RetryClientDecorator($client, $timer, $maxAttempts);

/** @var RequestInterface $request */
$response = $http->sendRequest($request);
```
