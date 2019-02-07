# SecureHeaders PSR-7 Adapter [![Build Status](https://travis-ci.org/SecureHeaders/PsrAdapter.svg?branch=master)](https://travis-ci.org/SecureHeaders/PsrAdapter) [![Build Status](https://ci.appveyor.com/api/projects/status/github/secureheaders/psradapter?branch=master&svg=true&retina=true)](https://ci.appveyor.com/project/aidantwoods/psradapter)

A PSR-7 adapter for [SecureHeaders](https://github.com/aidantwoods/SecureHeaders).
For more information on adapters, see [Framework Integration](https://github.com/aidantwoods/SecureHeaders/wiki/Framework-Integration) in the SecureHeaders Wiki.

## Installation

`composer require secureheaders/psradapter`

## Usage (Middleware)

Assuming you have a middleware runner in the `$middleware` variable:

```php
// Configure SecureHeaders
$headers = new Aidantwoods\SecureHeaders\SecureHeaders;
$headers->strictMode();

// Instantiate the middleware with the SecureHeaders object
$applyHeaders = new SecureHeaders\PsrHttpAdapter\ApplySecureHeaders($headers);

// Add the middleware to your stack as usual
$middleware->add($applyHeaders);

// Run your middleware as usual
$response = $middleware->run($serverRequest);

```

## Usage (Adapter alone)

Assuming you already have a PSR-7 response object (e.g. returned from a previous middleware) in the `$response` variable:

```php
// Configure SecureHeaders
$headers = new Aidantwoods\SecureHeaders\SecureHeaders;
$headers->strictMode();

// Instantiate the adapter with your response object
$adapter = new SecureHeaders\PsrHttpAdapter\Psr7Adapter($response);

// Apply your SecureHeaders configuration
$headers->apply($adapter);

// And finally retrieve the updated HTTP response object
$response = $adapter->getSecuredResponse();
```
