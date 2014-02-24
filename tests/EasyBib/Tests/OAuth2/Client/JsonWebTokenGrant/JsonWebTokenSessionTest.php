<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\JsonWebTokenGrant\ServerConfig;
use EasyBib\OAuth2\Client\JsonWebTokenGrant\JsonWebTokenSession;
use EasyBib\OAuth2\Client\TokenStore;
use EasyBib\Tests\Mocks\OAuth2\Client\ResourceRequest;
use Guzzle\Http\Client;
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
