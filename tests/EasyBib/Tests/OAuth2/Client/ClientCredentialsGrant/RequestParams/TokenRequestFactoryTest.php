<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant\RequestParams;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\TokenRequest;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\TokenRequestFactory;

class TokenRequestFactoryTest extends TestCase
{
    public function testCreate()
    {
        $tokenRequestFactory = new TokenRequestFactory(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope
        );

        $this->assertInstanceOf(TokenRequest::class, $tokenRequestFactory->create());
    }
}
