<?php

namespace EasyBib\Tests\OAuth2\Client;

use EasyBib\OAuth2\Client\SimpleSession;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\TokenRequest;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\TokenRequestFactory;
use EasyBib\OAuth2\Client\TokenStore;
use EasyBib\Tests\Mocks\OAuth2\Client\ResourceRequest;
use EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant\RequestParams\TestCase;
use Guzzle\Http\Client;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SimpleSessionTest extends TestCase
{
    /**
     * @var Session
     */
    private $tokenSession;

    /**
     * @var TokenStore
     */
    private $tokenStore;

    /**
     * @var SimpleSession
     */
    private $session;

    public function setUp()
    {
        parent::setUp();

        $this->tokenSession = new Session(new MockArraySessionStorage());
        $this->tokenStore = new TokenStore($this->tokenSession);
        $this->session = $this->createParamsSession();
    }

    public function testGetTokenWhenNotSet()
    {
        $token = 'ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockResponses);

        $this->session->getToken();

        $this->shouldHaveMadeATokenRequest();
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    public function testResourceRequestWhenSet()
    {
        $token = 'ABC123';

        $this->given->iHaveATokenInSession($token, $this->tokenSession);
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    public function testResourceRequestWhenExpired()
    {
        $oldToken = 'ABC123';
        $newToken = 'XYZ987';

        $this->given->iHaveATokenInSession($oldToken, $this->tokenSession);
        $this->given->myTokenIsExpired($this->tokenSession);
        $this->given->iAmReadyToRespondToATokenRequest($newToken, $this->scope, $this->mockResponses);

        (new ResourceRequest($this->session))->execute();

        $this->shouldHaveMadeATokenRequest();
        $this->shouldHaveTokenInHeaderForResourceRequests($newToken);
    }

    private function shouldHaveTokenInHeaderForResourceRequests($token)
    {
        $lastRequest = (new ResourceRequest($this->session))->execute();

        $this->assertEquals($token, $this->tokenStore->getToken());
        $this->assertEquals('Bearer ' . $token, $lastRequest->getHeader('Authorization'));
    }

    private function shouldHaveMadeATokenRequest()
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

    /**
     * @return SimpleSession
     */
    private function createParamsSession()
    {
        $tokenRequestFactory = new TokenRequestFactory(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope
        );

        $session = new SimpleSession($tokenRequestFactory);
        $session->setTokenStore($this->tokenStore);

        return $session;
    }
}
