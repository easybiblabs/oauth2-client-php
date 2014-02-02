<?php

namespace EasyBib\Tests\OAuth2\Client;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ClientConfig;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ServerConfig;
use EasyBib\Tests\Mocks\OAuth2\Client\MockTokenStore;
use Guzzle\Http\Client;
use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\Plugin\Mock\MockPlugin;

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
     * @var MockTokenStore
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
     * @var AuthorizationResponse
     */
    protected $authorization;

    public function __construct()
    {
        parent::__construct();

        $this->given = new Given();
    }

    public function setUp()
    {
        parent::setUp();

        $this->clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'redirect_url' => 'http://myapp.example.com/',
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

        $this->tokenStore = new MockTokenStore();
        $this->authorization = new AuthorizationResponse(['code' => 'ABC123']);
    }

    public function shouldHaveMadeATokenRequest()
    {
        $lastRequest = $this->history->getLastRequest();

        $expectedParams = [
            'grant_type' => 'authorization_code',
            'code' => $this->authorization->getCode(),
            'redirect_uri' => $this->clientConfig->getParams()['redirect_url'],
            'client_id' => $this->clientConfig->getParams()['client_id'],
        ];

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($expectedParams, $lastRequest->getPostFields()->toArray());
        $this->assertEquals($this->apiBaseUrl . '/oauth/token', $lastRequest->getUrl());
    }
}
