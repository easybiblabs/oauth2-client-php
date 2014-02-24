<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasicTokenRequest;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;

class HttpBasicTokenRequestTest extends TestCase
{
    public function testSend()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockResponses);

        $tokenRequest = new HttpBasicTokenRequest(
            $this->httpBasicClientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope
        );

        $tokenResponse = $tokenRequest->send();

        $this->shouldHaveMadeAnHttpBasicTokenRequest();
        $this->assertInstanceOf(TokenResponse::class, $tokenResponse);
        $this->assertEquals($token, $tokenResponse->getToken());
    }

    private function shouldHaveMadeAnHttpBasicTokenRequest()
    {
        $lastRequest = $this->history->getLastRequest();

        $configParams = $this->httpBasicClientConfig->getParams();

        $expectedUrl = sprintf(
            '%s%s',
            $this->apiBaseUrl,
            $this->serverConfig->getParams()['token_endpoint']
        );

        $expectedPostParams = [
            'grant_type' => HttpBasicTokenRequest::GRANT_TYPE,
        ];

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($configParams['client_id'], $lastRequest->getUsername());
        $this->assertEquals($configParams['client_password'], $lastRequest->getPassword());
        $this->assertEquals($expectedPostParams, $lastRequest->getPostFields()->toArray());
        $this->assertEquals($expectedUrl, $lastRequest->getUrl());
    }
}
