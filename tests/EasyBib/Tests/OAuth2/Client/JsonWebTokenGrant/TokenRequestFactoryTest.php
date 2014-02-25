<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\JsonWebTokenGrant\TokenRequestFactory;
use EasyBib\OAuth2\Client\JsonWebTokenGrant\TokenRequest;

class TokenRequestFactoryTest extends TestCase
{
    public function testCreate()
    {
        $tokenRequestFactory = new TokenRequestFactory(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope,
            $this->baseTime
        );

        $this->assertInstanceOf(TokenRequest::class, $tokenRequestFactory->create());
    }
}
