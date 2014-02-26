<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\TokenRequest;

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
            $this->authorization
        );

        $tokenResponse = $tokenRequest->send();

        $this->shouldHaveMadeATokenRequest();

        $class = '\EasyBib\OAuth2\Client\TokenResponse\TokenResponse';
        $this->assertInstanceOf($class, $tokenResponse);
        $this->assertEquals($token, $tokenResponse->getToken());
    }
}
