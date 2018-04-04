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

namespace GrahamCampbell\Tests\Bitbucket;

use Bitbucket\Client;
use GrahamCampbell\Bitbucket\Authenticators\AuthenticatorFactory;
use GrahamCampbell\Bitbucket\BitbucketFactory;
use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use Mockery;

/**
 * This is the bitbucket factory test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class BitbucketFactoryTest extends AbstractTestBenchTestCase
{
    public function testMakeStandard()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'oauth']);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeStandardExplicitCache()
    {
        $factory = $this->getFactory();

        $factory[1]->shouldReceive('store')->once()->with(null)->andReturn(Mockery::mock(Repository::class));

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'oauth', 'cache' => true]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeStandardNamedCache()
    {
        $factory = $this->getFactory();

        $factory[1]->shouldReceive('store')->once()->with('foo')->andReturn(Mockery::mock(Repository::class));

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'oauth', 'cache' => 'foo']);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeStandardNoCacheOrBackoff()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'oauth', 'cache' => false, 'backoff' => false]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeStandardExplicitBackoff()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'oauth', 'backoff' => true]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeStandardExplicitUrl()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['token' => 'your-token', 'method' => 'oauth', 'url' => 'https://api.example.com']);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeNoneMethod()
    {
        $factory = $this->getFactory();

        $client = $factory[0]->make(['method' => 'none']);

        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unsupported authentication method [bar].
     */
    public function testMakeInvalidMethod()
    {
        $factory = $this->getFactory();

        $factory[0]->make(['method' => 'bar']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The bitbucket factory requires an auth method.
     */
    public function testMakeEmpty()
    {
        $factory = $this->getFactory();

        $factory[0]->make([]);
    }

    protected function getFactory()
    {
        $cache = Mockery::mock(Factory::class);

        return [new BitbucketFactory(new AuthenticatorFactory(), $cache), $cache];
    }
}
