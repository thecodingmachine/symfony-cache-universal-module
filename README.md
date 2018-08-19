[![Latest Stable Version](https://poser.pugx.org/thecodingmachine/symfony-cache-universal-module/v/stable)](https://packagist.org/packages/thecodingmachine/symfony-cache-universal-module)
[![Latest Unstable Version](https://poser.pugx.org/thecodingmachine/symfony-cache-universal-module/v/unstable)](https://packagist.org/packages/thecodingmachine/symfony-cache-universal-module)
[![License](https://poser.pugx.org/thecodingmachine/symfony-cache-universal-module/license)](https://packagist.org/packages/thecodingmachine/symfony-cache-universal-module)
[![Build Status](https://travis-ci.org/thecodingmachine/symfony-cache-universal-module.svg?branch=master)](https://travis-ci.org/thecodingmachine/symfony-cache-universal-module)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/symfony-cache-universal-module/badge.svg?branch=master&service=github)](https://coveralls.io/github/thecodingmachine/symfony-cache-universal-module?branch=master)

# Symfony cache universal module

This package integrates the Symfoncy Cache component in any [container-interop](https://github.com/container-interop/service-provider) compatible framework/container.

## Installation

```bash
$ composer require thecodingmachine/symfony-cache-universal-module
```

Once installed, you need to register the [`TheCodingMachine\SymfonyCacheServiceProvider`](src/SymfonyCacheServiceProvider.php) into your container.

If your container supports [thecodingmachine/discovery](https://github.com/thecodingmachine/discovery) integration, you have nothing to do. Otherwise, refer to your framework or container's documentation to learn how to register *service providers*.

## Introduction

This service provider is meant to create both PSR-16 caches `Psr\SimpleCache\CacheInterface` and PSR-6 cache pools `Psr\Cache\CacheItemPoolInterface` instance.

Out of the box, the instance should be usable with sensible defaults. We tried to keep the defaults usable for most of the developer, while still providing best performances for the server:

- the provided caches are made of chainable caches
- the first level is an ArrayCache (in-memory) for fast access to already fetched values
- the second level is an APCu cache, with a *PhpFilesCache* fallback if the APCu extension is not available.

Note: the Symfony cache component provides a lot of adapters for a lot of platforms.
This service provider does not attempt to map all the caches provided but instead focuses on sane defaults.

### PSR-16 Usage

```php
use Psr\SimpleCache\CacheInterface

$cache = $container->get(CacheInterface::class);
echo $cachePool->get('my_cached_value');
```

### PSR-6 Usage

```php
use Psr\Cache\CacheItemPoolInterface

$cachePool = $container->get(CacheItemPoolInterface::class);
echo $cachePool->getItem('my_cached_value')->get();
```

## Expected values / services

This *service provider* expects the following configuration / services to be available:

| Name                        | Compulsory | Description                            |
|-----------------------------|------------|----------------------------------------|
| `symfony.cache.namespace`   | *no*       | The namespace for the cache. Defaults to ''.  |
| `symfony.cache.defaultLifetime` | *no*       | The default life time for the cache. Defaults to 0 (no limit).  |
| `symfony.cache.version`     | *no*       | The version of the cache (if changed, the cache is purged)  |
| `symfony.cache.files.directory` | *no*       | The directory where cached files will be stored. Defaults to a directory in the temporary system directory.  |

## Provided services

This *service provider* provides the following services:

| Service name                | Description                          |
|-----------------------------|--------------------------------------|
| `CacheInterface::class`     | Alias to `ChainCache::class` |
| `ChainCache::class`         | A composite cache that chains calls to several cache backend |
| `symfony.cache.chained.caches` | The list of chained caches used by the `ChainCache::class` instance. This value is a `SplPriorityQueue` that can be extended easily. |
| `ArrayCache::class`         | An in-memory cache |
| `NullCache::class`          | A cache that caches nothing |
| `ApcuCache::class`          | A cache with an APCu backend |
| `PhpFilesCache::class`      | A cache with PHP files as backend |


<small>Project template courtesy of <a href="https://github.com/thecodingmachine/service-provider-template">thecodingmachine/service-provider-template</a></small>
