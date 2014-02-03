<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Session;
use EasyBib\Tests\Mocks\OAuth2\Client\ExceptionMockRedirector;
use EasyBib\Tests\Mocks\OAuth2\Client\MockRedirectException;
use EasyBib\Tests\OAuth2\Client\TestCase;
use Guzzle\Http\Client;
use Guzzle\Plugin\History\HistoryPlugin;

class SessionTest extends TestCase
{
    /**
     * @var Session
     */
    private $session;

    public function setUp()
    {
        parent::setUp();

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
        $this->given->iAmReadyToRespondToATokenRequest($newToken, $this->mockResponses);

        $this->makeResourceRequest();

        $this->shouldHaveMadeATokenRefreshRequest($refreshToken);
        $this->shouldHaveTokenInHeaderForResourceRequests($newToken);
    }

    public function testResourceRequestWhenExpiredHavingNoRefreshToken()
    {
        $oldToken = 'ABC123';

        $this->given->iHaveATokenInSession($oldToken, $this->tokenSession);
        $this->given->myTokenIsExpired($this->tokenSession);

        $this->expectRedirectToAuthorizationEndpoint();
        $this->makeResourceRequest();
    }

    public function testHandleAuthorizationResponse()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->mockResponses);

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
        $lastRequest = $this->makeResourceRequest();

        $this->assertEquals($token, $this->tokenStore->getToken());
        $this->assertEquals('Bearer ' . $token, $lastRequest->getHeader('Authorization'));
    }

    /**
     * @return \Guzzle\Http\Message\RequestInterface
     */
    private function makeResourceRequest()
    {
        $history = new HistoryPlugin();

        $httpClient = new Client();
        $httpClient->addSubscriber($history);

        $this->session->addResourceSubscriber($httpClient);

        $request = $httpClient->get('http://example.org');
        $request->send();

        return $history->getLastRequest();
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

        $this->setExpectedException(MockRedirectException::class, $message);
    }

    /**
     * @return Session
     */
    private function createSession()
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
}
