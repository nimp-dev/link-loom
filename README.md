# LinkLoom - PHP URL Shortener Library

![Tests](https://github.com/nimp-dev/link-loom/actions/workflows/tests.yml/badge.svg)
![PHPStan](https://github.com/nimp-dev/link-loom/actions/workflows/phpstan.yml/badge.svg)
![Code Coverage](https://codecov.io/gh/nimp-dev/link-loom/branch/main/graph/badge.svg)
![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-blue.svg)

A flexible, extensible URL shortener library for PHP applications with multiple storage backends and event-driven architecture.

## Features

- 🚀 **Multiple Storage Backends** - Redis, File, or bring your own
- 📝 **PSR-compliant Logging** - Built-in Monolog integration
- 🎯 **Event-Driven Architecture** - Hook into URL processing lifecycle
- 🔧 **Extensible Design** - Easy to customize validators, code generators, and more
- 💪 **Type-Safe** - Full PHP type hints and exceptions

## Installation

```bash
composer require nimp/link-loom
```

## Quick Start
### Basic Usage with Redis
```php
<?php

use Nimp\LinkLoom\UrlShortenerBuilder;
use Redis;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$shortener = (new UrlShortenerBuilder())
    ->withRedisRepository($redis)
    ->build();

// Shorten URL
$code = $shortener->encode('https://example.com/long-url');
echo "Short code: " . $code;

// Expand URL
$url = $shortener->decode($code);
echo "Original URL: " . $url;
```
### With Logging
```php
$shortener = (new UrlShortenerBuilder())
    ->withRedisRepository($redis)
    ->withLogger('/path/to/urls.log')
    ->build();
```
### Advanced Configuration
```php
$shortener = (new UrlShortenerBuilder())
    ->withCustomRepository(new MySQLRepository())
    ->withCodeGenerator(new MyCustomCodeGenerator())
    ->withCustomValidator(new MyCustomValidator())
    ->build();
```
### Multiple Event Listeners
```php
$shortener = (new UrlShortenerBuilder())
    ->withRedisRepository($redis)
    ->withLogger('/path/to/urls.log')
    ->addListener(new AnalyticsListener())
    ->addListener(new CacheListener())
    ->build();
```
### Custom Event Dispatcher
```php
$shortener = (new UrlShortenerBuilder())
    ->withRedisRepository($redis)
    ->withEventDispatcher($myCustomDispatcher)
    ->build();
```

## Event System

LinkLoom provides a comprehensive event system for monitoring the URL shortening lifecycle using the Observer pattern.

### Available Events

All events extend `BaseShortenerEvent` and provide access to the `UrlShortener` instance:

```php
$event->context; // UrlShortener instance
```

| Event | Trigger | Data                  |
|-------|---------|-----------------------|
| `EncodeStartEvent` | Before URL encoding starts | `url`, `context`      |
| `EncodeSuccessEvent` | After successful URL encoding | `url`, `code`, `context` |
| `DecodeStartEvent` | Before code decoding starts | `code` , `context`    |
| `DecodeSuccessEvent` | After successful code decoding | `code`, `url`, `context` |
| `ValidateErrorEvent` | When URL validation fails | `url`, `message` |
| `GetFromStorageErrorEvent` | When storage read fails | `code`, `message`, `context` |
| `SaveErrorEvent` | When storage save fails | `message`, `context`|


## UrlShortener Methods
Method	Description
```
encode(string $url): string	Shorten URL and return code
decode(string $code): string	Expand code to original URL
```
