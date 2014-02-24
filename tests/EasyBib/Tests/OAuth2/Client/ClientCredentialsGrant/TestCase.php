<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasicClientConfig;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\ParamsClientConfig;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\ParamsTokenRequest;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\Tests\OAuth2\Client\Given;
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
     * @var ParamsClientConfig
     */
    protected $paramsClientConfig;

    /**
     * @var HttpBasicClientConfig
     */
    protected $httpBasicClientConfig;

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

        $this->paramsClientConfig = new ParamsClientConfig([
            'client_id' => 'client_123',
            'client_secret' => 'secret_456',
        ]);

        $this->httpBasicClientConfig = new HttpBasicClientConfig([
            'client_id' => 'client_123',
            'client_password' => 'secret_456',
        ]);

        $this->serverConfig = new ServerConfig([
            'token_endpoint' => '/oauth/token',
        ]);

        $this->httpClient = new Client($this->apiBaseUrl);
        $this->mockResponses = new MockPlugin();
        $this->history = new HistoryPlugin();
        $this->httpClient->addSubscriber($this->mockResponses);
        $this->httpClient->addSubscriber($this->history);

        $this->scope = new Scope(['USER_READ', 'DATA_READ_WRITE']);
    }

    protected function shouldHaveMadeAParamsTokenRequest()
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

    protected function shouldHaveMadeAnHttpBasicTokenRequest()
    {
        $lastRequest = $this->history->getLastRequest();

        $expectedUrl = sprintf(
            '%s%s',
            $this->apiBaseUrl,
            $this->serverConfig->getParams()['token_endpoint']
        );

        $configParams = $this->httpBasicClientConfig->getParams();

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($configParams['client_id'], $lastRequest->getUsername());
        $this->assertEquals($configParams['client_password'], $lastRequest->getPassword());
        $this->assertEquals($expectedUrl, $lastRequest->getUrl());
    }
}
