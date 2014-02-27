<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\StatelessSession;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\TokenStore;
use EasyBib\Tests\Mocks\OAuth2\Client\ExceptionMockRedirector;
use EasyBib\Tests\Mocks\OAuth2\Client\ResourceRequest;
use Guzzle\Http\Client;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class StatelessSessionTest extends TestCase
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
     * @var StatelessSession
     */
    private $session;

    public function setUp()
    {
        parent::setUp();

        $this->tokenSession = new Session(new MockArraySessionStorage());
        $this->tokenStore = new TokenStore($this->tokenSession);
        $this->session = $this->createSession();
    }

    public function testGetTokenWhenNotSet()
    {
        $this->expectRedirectToAuthorizationEndpoint();
        $this->session->getToken();
    }

    public function testResourceRequestWhenSet()
    {
        $token = 'ABC123';

        $this->given->iHaveATokenInSession($token, $this->tokenSession);
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    public function testResourceRequestWhenExpiredHavingRefreshToken()
    {
        $oldToken = 'ABC123';
        $newToken = 'XYZ987';
        $refreshToken = 'REFRESH_456';

        $this->given->iHaveATokenInSession($oldToken, $this->tokenSession);
        $this->given->iHaveARefreshToken($refreshToken, $this->tokenSession);
        $this->given->myTokenIsExpired($this->tokenSession);
        $this->given->iAmReadyToRespondToATokenRequest($newToken, $this->scope, $this->mockResponses);

        (new ResourceRequest($this->session))->execute();

        $this->shouldHaveMadeATokenRefreshRequest($refreshToken);
        $this->shouldHaveTokenInHeaderForResourceRequests($newToken);
    }

    public function testResourceRequestWhenExpiredHavingNoRefreshToken()
    {
        $oldToken = 'ABC123';

        $this->given->iHaveATokenInSession($oldToken, $this->tokenSession);
        $this->given->myTokenIsExpired($this->tokenSession);

        $this->expectRedirectToAuthorizationEndpoint();
        (new ResourceRequest($this->session))->execute();
    }

    public function testHandleAuthorizationResponse()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockResponses);

        $this->session->handleAuthorizationResponse($this->authorization);

        $this->shouldHaveMadeATokenRequest($token);
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    /**
     * @param string $refreshToken
     */
    private function shouldHaveMadeATokenRefreshRequest($refreshToken)
    {
        $lastRequest = $this->history->getLastRequest();

        $this->assertEquals(
            $this->apiBaseUrl . $this->serverConfig->getParams()['token_endpoint'],
            $lastRequest->getUrl()
        );

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals('refresh_token', $lastRequest->getPostFields()['grant_type']);
        $this->assertEquals($refreshToken, $lastRequest->getPostFields()['refresh_token']);
    }

    private function shouldHaveTokenInHeaderForResourceRequests($token)
    {
        $lastRequest = (new ResourceRequest($this->session))->execute();

        $this->assertEquals($token, $this->tokenStore->getToken());
        $this->assertEquals('Bearer ' . $token, $lastRequest->getHeader('Authorization'));
    }

    private function expectRedirectToAuthorizationEndpoint()
    {
        $message = vsprintf(
            'Redirecting to %s?response_type=%s&client_id=%s&redirect_url=%s&scope=%s',
            [
                $this->apiBaseUrl . $this->serverConfig->getParams()['authorization_endpoint'],
                'code',
                'client_123',
                urlencode($this->clientConfig->getParams()['redirect_url']),
                'USER_READ+DATA_READ_WRITE',
            ]
        );

        $exceptionClass = '\EasyBib\Tests\Mocks\OAuth2\Client\MockRedirectException';
        $this->setExpectedException($exceptionClass, $message);
    }

    /**
     * @return StatelessSession
     */
    private function createSession()
    {
        $session = new StatelessSession(
            $this->httpClient,
            new ExceptionMockRedirector(),
            $this->clientConfig,
            $this->serverConfig
        );

        $session->setTokenStore($this->tokenStore);
        $session->setScope($this->scope);

        return $session;
    }
}
