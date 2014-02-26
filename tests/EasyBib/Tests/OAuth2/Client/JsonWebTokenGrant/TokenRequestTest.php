<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\JsonWebTokenGrant\TokenRequest;

class TokenRequestTest extends TestCase
{
    public function testSend()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockResponses);

        $tokenRequest = new TokenRequest(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope,
            $this->baseTime
        );

        $tokenResponse = $tokenRequest->send();
        $class = '\EasyBib\OAuth2\Client\TokenResponse\TokenResponse';

        $this->shouldHaveMadeATokenRequest();
        $this->assertInstanceOf($class, $tokenResponse);
        $this->assertEquals($token, $tokenResponse->getToken());
    }
}
