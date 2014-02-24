<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\ParamsTokenRequest;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\ParamsTokenRequestFactory;

class ParamsTokenRequestFactoryTest extends TestCase
{
    public function testCreate()
    {
        $tokenRequestFactory = new ParamsTokenRequestFactory(
            $this->paramsClientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope
        );

        $this->assertInstanceOf(ParamsTokenRequest::class, $tokenRequestFactory->create());
    }
}
