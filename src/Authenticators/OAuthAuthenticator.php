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

namespace GrahamCampbell\Bitbucket\Authenticators;

use Bitbucket\Client;
use InvalidArgumentException;

/**
 * This is the oauth authenticator class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class OAuthAuthenticator extends AbstractAuthenticator implements AuthenticatorInterface
{
    /**
     * Authenticate the client, and return it.
     *
     * @param string[] $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Bitbucket\Client
     */
    public function authenticate(array $config)
    {
        if (!$this->client) {
            throw new InvalidArgumentException('The client instance was not given to the oauth authenticator.');
        }

        if (!array_key_exists('token', $config)) {
            throw new InvalidArgumentException('The oauth authenticator requires a token.');
        }

        $this->client->authenticate(Client::AUTH_OAUTH_TOKEN, $config['token']);

        return $this->client;
    }
}
