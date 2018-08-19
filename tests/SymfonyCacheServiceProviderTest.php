<?php

namespace TheCodingMachine;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Simplex\Container;
use Symfony\Component\Cache\Simple\ChainCache;
use Symfony\Component\Cache\Simple\NullCache;
use Symfony\Component\Cache\Simple\PhpFilesCache;

class SymfonyCacheServiceProviderTest extends TestCase
{
    public function testServiceProvider(): void
    {
        $container = new Container([new SymfonyCacheServiceProvider()]);
        $cache = $container->get(CacheInterface::class);

        $this->assertInstanceOf(ChainCache::class, $cache);

        $nullCache = $container->get(NullCache::class);
        $this->assertInstanceOf(NullCache::class, $nullCache);

        $filesCache = $container->get(PhpFilesCache::class);
        $this->assertInstanceOf(PhpFilesCache::class, $filesCache);

    }
}
