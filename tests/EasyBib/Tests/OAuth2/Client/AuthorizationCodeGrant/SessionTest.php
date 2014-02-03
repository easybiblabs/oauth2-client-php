<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Session;
use EasyBib\Tests\Mocks\OAuth2\Client\ExceptionMockRedirector;
use EasyBib\Tests\Mocks\OAuth2\Client\MockRedirectException;
use EasyBib\Tests\OAuth2\Client\TestCase;
use Guzzle\Http\Client;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SessionTest extends TestCase
{
    /**
     * @var string
     */
    private $redirectUrl = 'http://myapp.example.org/handle/oauth';

    /**
     * @var Session
     */
    private $session;

    public function setUp()
    {
        parent::setUp();

        $this->session = $this->getSession();
    }

    public function testIsTokenExpired()
    {
        $this->tokenStore->setExpiresAt(null);
        $this->assertFalse($this->session->isTokenExpired());

        $this->tokenStore->setExpiresAt(time() + 1000);
        $this->assertFalse($this->session->isTokenExpired());

        $this->tokenStore->setExpiresAt(time() - 100);
        $this->assertTrue($this->session->isTokenExpired());
    }

    public function testEnsureTokenWhenNotSet()
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

        $this->setExpectedException(MockRedirectException::class, $message);

        $this->session->ensureToken();
    }

    public function testEnsureTokenWhenSet()
    {
        $token = 'ABC123';

        $this->tokenStore->setToken($token);
        $this->session->ensureToken();

        $lastRequest = $this->makeRequest();

        $this->assertEquals(
            sprintf('Bearer %s', $token),
            $lastRequest->getHeader('Authorization')
        );
    }

    public function testEnsureTokenWhenExpiredHavingRefreshToken()
    {
        $oldToken = 'ABC123';
        $newToken = 'XYZ987';
        $refreshToken = 'REFRESH_456';

        $this->given->iHaveAnExpiredToken($oldToken, $this->tokenStore);
        $this->given->iHaveARefreshToken($refreshToken, $this->tokenStore);
        $this->given->iAmReadyToRespondToATokenRequest($newToken, $this->mockResponses);

        $this->session->ensureToken();

        $this->shouldHaveMadeATokenRefreshRequest($refreshToken);
        $this->shouldHaveTokenInHeaderForNewRequests($newToken);
    }

    public function testEnsureTokenWhenExpiredHavingNoRefreshToken()
    {
        $this->markTestIncomplete();
    }

    public function testHandleAuthorizationResponse()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->mockResponses);

        $this->session->handleAuthorizationResponse($this->authorization);

        $this->shouldHaveMadeATokenRequest($token);
        $this->shouldHaveTokenInHeaderForNewRequests($token);
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

    private function shouldHaveTokenInHeaderForNewRequests($token)
    {
        $lastRequest = $this->makeRequest();

        $this->assertEquals($token, $this->tokenStore->getToken());
        $this->assertEquals('Bearer ' . $token, $lastRequest->getHeader('Authorization'));
    }

    /**
     * @return Session
     */
    private function getSession()
    {
        $session = new Session(
            $this->tokenStore,
            $this->httpClient,
            new ExceptionMockRedirector(),
            $this->clientConfig,
            $this->serverConfig
        );

        $scope = new Scope(['USER_READ', 'DATA_READ_WRITE']);
        $session->setScope($scope);

        return $session;
    }

    /**
     * @return \Guzzle\Http\Message\RequestInterface
     */
    private function makeRequest()
    {
        $request = $this->httpClient->get('http://example.org');
        $request->send();

        return $this->history->getLastRequest();
    }
}
