<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\JsonWebTokenGrant\ClientConfig;
use EasyBib\OAuth2\Client\JsonWebTokenGrant\ServerConfig;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\JsonWebTokenGrant\JsonWebTokenSession;
use EasyBib\OAuth2\Client\TokenStore;
use EasyBib\Tests\Mocks\OAuth2\Client\ExceptionMockRedirector;
use EasyBib\Tests\Mocks\OAuth2\Client\MockRedirectException;
use EasyBib\Tests\OAuth2\Client\Given;
use Guzzle\Http\Client;
use Guzzle\Plugin\History\HistoryPlugin;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class JsonWebTokenSessionTest extends TestCase
{
    private $clientConfig;

    private $serverConfig;

    /**
     * @var JsonWebTokenSession
     */
    private $session;

    private $tokenSession;

    private $tokenStore;

    public function setUp()
    {
        parent::setUp();

        $this->given = new Given();

        $this->clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'client_secret' => 'client_secret_456',
        ]);

        $this->serverConfig = new ServerConfig([
            'authorization_endpoint' => '/oauth/authorize',
            'token_endpoint' => '/oauth/token',
        ]);

        $this->tokenSession = new Session(new MockArraySessionStorage());
        $this->tokenStore = new TokenStore($this->tokenSession);

        $this->session = $this->createSession();
    }

    public function testGetTokenWhenNotSet()
    {
        $this->markTestIncomplete();
        $token = 'ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->mockResponses);

        $this->session->getToken();

        $this->shouldHaveMadeATokenRequest();
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    public function testResourceRequestWhenSet()
    {
        $this->markTestIncomplete();
        $token = 'ABC123';

        $this->given->iHaveATokenInSession($token, $this->tokenSession);
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    public function testResourceRequestWhenExpired()
    {
        $this->markTestIncomplete();
        $oldToken = 'ABC123';
        $newToken = 'XYZ987';
        $refreshToken = 'REFRESH_456';

        $this->given->iHaveATokenInSession($oldToken, $this->tokenSession);
        $this->given->myTokenIsExpired($this->tokenSession);
        $this->given->iAmReadyToRespondToATokenRequest($newToken, $this->mockResponses);

        $this->makeResourceRequest();

        $this->shouldHaveMadeATokenRequest();
        $this->shouldHaveTokenInHeaderForResourceRequests($newToken);
    }

    private function shouldHaveTokenInHeaderForResourceRequests($token)
    {
        $lastRequest = $this->makeResourceRequest();

        $this->assertEquals($token, $this->tokenStore->getToken());
        $this->assertEquals('Bearer ' . $token, $lastRequest->getHeader('Authorization'));
    }

    /**
     * @todo duplicate code?
     * @return \Guzzle\Http\Message\RequestInterface
     */
    private function makeResourceRequest()
    {
        $history = new HistoryPlugin();

        $httpClient = new Client();
        $httpClient->addSubscriber($history);

        $this->session->addResourceClient($httpClient);

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
     * @return JsonWebTokenSession[
     */
    private function createSession()
    {
        $session = new JsonWebTokenSession(
            $this->httpClient,
            new ExceptionMockRedirector(),
            $this->clientConfig,
            $this->serverConfig
        );

        $session->setTokenStore($this->tokenStore);

        $scope = new Scope(['USER_READ', 'DATA_READ_WRITE']);
        $session->setScope($scope);

        return $session;
    }
}
