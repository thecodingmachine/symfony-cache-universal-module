<?php
declare(strict_types=1);

namespace TheCodingMachine;

use Psr\SimpleCache\CacheInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Simple\ApcuCache;
use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\Cache\Simple\ChainCache;
use Symfony\Component\Cache\Simple\NullCache;
use Symfony\Component\Cache\Simple\PhpFilesCache;
use TheCodingMachine\Funky\Annotations\Factory;
use TheCodingMachine\Funky\ServiceProvider;

class SymfonyCacheServiceProvider extends ServiceProvider
{
    /**
     * @Factory(name="symfony.cache.namespace")
     */
    public static function getNamespace(): string
    {
        return '';
    }

    /**
     * @Factory(name="symfony.cache.defaultLifetime")
     */
    public static function getDefaultLifetime(): int
    {
        return 0;
    }

    /**
     * @Factory(name="symfony.cache.version")
     */
    public static function getVersion(): ?string
    {
        return null;
    }

    /**
     * @Factory(name="symfony.cache.files.directory")
     */
    public static function getFilesDirectory(): ?string
    {
        return null;
    }

    /**
     * @Factory(name="symfony.cache.chained.caches")
     */
    public static function getChainedCaches(ContainerInterface $container, ArrayCache $arrayCache): \SplPriorityQueue
    {

        // Let's put the arraycache driver first.
        $queue = new \SplPriorityQueue();

        $queue->insert($arrayCache, 1000);

        // Now, let's put APCu if available.
        if (ApcuCache::isSupported()) {
            $queue->insert($container->get(ApcuCache::class), 10);
        } else {
            // Else, let's enable the filesystem by default.
            $queue->insert($container->get(PhpFilesCache::class), 0);
        }

        return $queue;
    }

    /**
     * @Factory(aliases={CacheInterface::class})
     */
    public static function createChainCache(ContainerInterface $container): ChainCache
    {
        return new ChainCache(\iterator_to_array($container->get('symfony.cache.chained.caches')), $container->get('symfony.cache.defaultLifetime'));
    }

    /**
     * @Factory()
     */
    public static function createArrayCache(ContainerInterface $container): ArrayCache
    {
        // Note: the array cache does not store the data serialized by default (because we uses it for high performance).
        return new ArrayCache($container->get('symfony.cache.defaultLifetime'), false);
    }

    /**
     * @Factory()
     */
    public static function createNullCache(): NullCache
    {
        // Note: the array cache does not store the data serialized by default (because we uses it for high performance).
        return new NullCache();
    }

    /**
     * @Factory()
     */
    public static function createApcuCache(ContainerInterface $container): ApcuCache
    {
        // Note: the array cache does not store the data serialized by default (because we uses it for high performance).
        return new ApcuCache($container->get('symfony.cache.namespace'),
            $container->get('symfony.cache.defaultLifetime'),
            $container->get('symfony.cache.version'));
    }

    /**
     * @Factory()
     */
    public static function createPhpFilesCache(ContainerInterface $container): PhpFilesCache
    {
        // Note: the array cache does not store the data serialized by default (because we uses it for high performance).
        return new PhpFilesCache($container->get('symfony.cache.namespace'),
            $container->get('symfony.cache.defaultLifetime'),
            $container->get('symfony.cache.files.directory')
            );
    }
}
