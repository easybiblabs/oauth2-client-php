<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\JsonWebTokenGrant\ClientConfig;
use EasyBib\OAuth2\Client\JsonWebTokenGrant\ServerConfig;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\JsonWebTokenGrant\JsonWebTokenSession;
use EasyBib\OAuth2\Client\TokenStore;
use EasyBib\Tests\OAuth2\Client\Given;
use Guzzle\Http\Client;
use Guzzle\Plugin\History\HistoryPlugin;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class JsonWebTokenSessionTest extends TestCase
{
    /**
     * @var JsonWebTokenSession
     */
    private $session;

    /**
     * @var Session
     */
    private $tokenSession;

    /**
     * @var TokenStore
     */
    private $tokenStore;

    public function setUp()
    {
        parent::setUp();

        $this->tokenSession = new Session(new MockArraySessionStorage());
        $this->tokenStore = new TokenStore($this->tokenSession);

        $this->session = $this->createSession();
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

    /**
     * @return JsonWebTokenSession
     */
    private function createSession()
    {
        $session = new JsonWebTokenSession(
            $this->httpClient,
            $this->clientConfig,
            $this->serverConfig
        );

        $session->setTokenStore($this->tokenStore);
        $session->setScope($this->scope);
        $session->setBaseTime($this->baseTime);

        return $session;
    }
}
