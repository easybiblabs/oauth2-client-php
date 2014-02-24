<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasicTokenRequest;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasicTokenRequestFactory;

class HttpBasicTokenRequestFactoryTest extends TestCase
{
    public function testCreate()
    {
        $tokenRequestFactory = new HttpBasicTokenRequestFactory(
            $this->httpBasicClientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope
        );

        $this->assertInstanceOf(HttpBasicTokenRequest::class, $tokenRequestFactory->create());
    }
}
