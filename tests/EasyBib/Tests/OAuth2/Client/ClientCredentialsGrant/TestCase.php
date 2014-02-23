<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\ClientConfig;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\TokenRequest;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\TokenStore;
use EasyBib\Tests\OAuth2\Client\Given;
use Guzzle\Http\Client;
use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\Plugin\Mock\MockPlugin;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Given
     */
    protected $given;

    /**
     * @var string
     */
    protected $apiBaseUrl = 'http://data.easybib.example.com';

    /**
     * @var HistoryPlugin
     */
    protected $history;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var MockPlugin
     */
    protected $mockResponses;

    /**
     * @var Session
     */
    protected $tokenSession;

    /**
     * @var TokenStore
     */
    protected $tokenStore;

    /**
     * @var ClientConfig
     */
    protected $clientConfig;

    /**
     * @var ServerConfig
     */
    protected $serverConfig;

    /**
     * @var Scope
     */
    protected $scope;

    public function setUp()
    {
        parent::setUp();

        $this->given = new Given();

        $this->clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'client_secret' => 'secret_456',
        ]);

        $this->serverConfig = new ServerConfig([
            'authorization_endpoint' => '/oauth/authorize',
            'token_endpoint' => '/oauth/token',
        ]);

        $this->httpClient = new Client($this->apiBaseUrl);
        $this->mockResponses = new MockPlugin();
        $this->history = new HistoryPlugin();
        $this->httpClient->addSubscriber($this->mockResponses);
        $this->httpClient->addSubscriber($this->history);

        $this->tokenSession = new Session(new MockArraySessionStorage());
        $this->tokenStore = new TokenStore($this->tokenSession);

        $this->scope = new Scope(['USER_READ', 'DATA_READ_WRITE']);
    }

    protected function shouldHaveMadeATokenRequest()
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
}
