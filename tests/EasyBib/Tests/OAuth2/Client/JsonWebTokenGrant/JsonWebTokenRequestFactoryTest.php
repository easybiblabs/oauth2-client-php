<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\JsonWebTokenGrant\JsonWebTokenRequestFactory;
use EasyBib\OAuth2\Client\JsonWebTokenGrant\TokenRequest;

class JsonWebTokenRequestFactoryTest extends TestCase
{
    public function testCreate()
    {
        $tokenRequestFactory = new JsonWebTokenRequestFactory(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope,
            $this->baseTime
        );

        $this->assertInstanceOf(TokenRequest::class, $tokenRequestFactory->create());
    }
}
