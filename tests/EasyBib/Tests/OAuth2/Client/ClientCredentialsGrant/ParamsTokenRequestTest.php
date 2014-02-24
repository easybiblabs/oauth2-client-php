<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\ParamsTokenRequest;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;

class ParamsTokenRequestTest extends TestCase
{
    public function testSend()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockResponses);

        $tokenRequest = new ParamsTokenRequest(
            $this->paramsClientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope
        );

        $tokenResponse = $tokenRequest->send();

        $this->shouldHaveMadeAParamsTokenRequest();
        $this->assertInstanceOf(TokenResponse::class, $tokenResponse);
        $this->assertEquals($token, $tokenResponse->getToken());
    }

    private function shouldHaveMadeAParamsTokenRequest()
    {
        $lastRequest = $this->history->getLastRequest();

        $expectedParams = [
            'grant_type' => ParamsTokenRequest::GRANT_TYPE,
            'client_id' => $this->paramsClientConfig->getParams()['client_id'],
            'client_secret' => $this->paramsClientConfig->getParams()['client_secret'],
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
