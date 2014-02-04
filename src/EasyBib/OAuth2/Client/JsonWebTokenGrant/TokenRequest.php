<?php

namespace EasyBib\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\TokenRequestInterface;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;

class TokenRequest implements TokenRequestInterface
{
    /**
     * @return TokenResponse
     */
    public function send()
    {
        throw new \BadMethodCallException('send() not yet implemented');
    }
}
