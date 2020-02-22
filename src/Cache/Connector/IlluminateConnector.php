<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Bitbucket.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Bitbucket\Cache\Connector;

use GrahamCampbell\BoundedCache\BoundedCache;
use GrahamCampbell\Manager\ConnectorInterface;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Symfony\Component\Cache\Adapter\Psr16Adapter;

/**
 * This is the illuminate connector class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class IlluminateConnector implements ConnectorInterface
{
    /**
     * The minimum cache lifetime of 12 hours.
     *
     * @var int
     */
    const MIN_CACHE_LIFETIME = 43200;

    /**
     * The maximum cache lifetime of 48 hours.
     *
     * @var int
     */
    const MAX_CACHE_LIFETIME = 172800;

    /**
     * The cache factory instance.
     *
     * @var \Illuminate\Contracts\Cache\Factory|null
     */
    protected $cache;

    /**
     * Create a new illuminate connector instance.
     *
     * @param \Illuminate\Contracts\Cache\Factory|null $cache
     *
     * @return void
     */
    public function __construct(Factory $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Establish a cache connection.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Psr\Cache\CacheItemPoolInterface
     */
    public function connect(array $config)
    {
        $repository = $this->getRepository($config);

        return $this->getAdapter($repository, $config);
    }

    /**
     * Get the cache repository.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getRepository(array $config)
    {
        if (!$this->cache) {
            throw new InvalidArgumentException('Illuminate caching support not available.');
        }

        $name = Arr::get($config, 'connector');

        return $this->cache->store($name);
    }

    /**
     * Get the illuminate cache adapter.
     *
     * @param \Illuminate\Contracts\Cache\Repository $repository
     * @param array                                  $config
     *
     * @return \Symfony\Component\Cache\Adapter\AdapterInterface
     */
    protected function getAdapter(Repository $repository, array $config)
    {
        $min = Arr::get($config, 'min', self::MIN_CACHE_LIFETIME);
        $max = Arr::get($config, 'max', self::MAX_CACHE_LIFETIME);

        return new Psr16Adapter(
            new BoundedCache($repository, $min, $max)
        );
    }

    /**
     * Get the cache instance.
     *
     * @return \Illuminate\Contracts\Cache\Factory|null
     */
    public function getCache()
    {
        return $this->cache;
    }
}
