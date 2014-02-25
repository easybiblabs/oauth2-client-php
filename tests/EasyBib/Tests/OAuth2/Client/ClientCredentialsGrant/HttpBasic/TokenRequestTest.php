<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant\HttpBasic;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasic\TokenRequest;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;

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

        $this->shouldHaveMadeAnHttpBasicTokenRequest();
        $this->assertInstanceOf(TokenResponse::class, $tokenResponse);
        $this->assertEquals($token, $tokenResponse->getToken());
    }

    private function shouldHaveMadeAnHttpBasicTokenRequest()
    {
        $lastRequest = $this->history->getLastRequest();

        $configParams = $this->clientConfig->getParams();

        $expectedUrl = sprintf(
            '%s%s',
            $this->apiBaseUrl,
            $this->serverConfig->getParams()['token_endpoint']
        );

        $expectedPostParams = [
            'grant_type' => TokenRequest::GRANT_TYPE,
        ];

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($configParams['client_id'], $lastRequest->getUsername());
        $this->assertEquals($configParams['client_password'], $lastRequest->getPassword());
        $this->assertEquals($expectedPostParams, $lastRequest->getPostFields()->toArray());
        $this->assertEquals($expectedUrl, $lastRequest->getUrl());
    }
}
