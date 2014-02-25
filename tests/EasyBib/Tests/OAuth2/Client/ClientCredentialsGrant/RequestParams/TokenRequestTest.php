<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant\RequestParams;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\TokenRequest;

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
            $this->scope
        );

        $tokenResponse = $tokenRequest->send();
        $class = '\EasyBib\OAuth2\Client\TokenResponse\TokenResponse';

        $this->shouldHaveMadeAParamsTokenRequest();
        $this->assertInstanceOf($class, $tokenResponse);
        $this->assertEquals($token, $tokenResponse->getToken());
    }

    private function shouldHaveMadeAParamsTokenRequest()
    {
        $lastRequest = $this->history->getLastRequest();

        $expectedParams = [
            'grant_type' => TokenRequest::GRANT_TYPE,
            'client_id' => $this->clientConfig->getParams()['client_id'],
            'client_secret' => $this->clientConfig->getParams()['client_secret'],
        ];

        $expectedUrl = sprintf(
            '%s%s',
            $this->apiBaseUrl,
            $this->serverConfig->getParams()['token_endpoint']
        );

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($expectedParams, $lastRequest->getPostFields()->toArray());
        $this->assertEquals($expectedUrl, $lastRequest->getUrl());
    }
}
