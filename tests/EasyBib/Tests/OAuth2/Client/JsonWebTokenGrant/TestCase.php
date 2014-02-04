<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\JsonWebTokenGrant\ClientConfig;
use EasyBib\OAuth2\Client\JsonWebTokenGrant\TokenRequest;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\Tests\OAuth2\Client\Given;
use Guzzle\Http\Client;
use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\Plugin\Mock\MockPlugin;
use JWT;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $apiBaseUrl = 'http://data.easybib.example.com';

    /**
     * @var Given
     */
    protected $given;

    protected $clientConfig;

    protected $serverConfig;

    protected $httpClient;

    protected $history;

    protected $mockResponses;

    protected $scope;

    public function setUp()
    {
        parent::setUp();

        $this->given = new Given();

        $this->clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'client_secret' => 'client_secret_456',
            'subject' => 'user_987',
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

        $this->scope = new Scope(['USER_READ', 'DATA_READ_WRITE']);
    }

    public function shouldHaveMadeATokenRequest()
    {
        $lastRequest = $this->history->getLastRequest();
        $requestParams = $this->getRequestParams();

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($this->getTokenEndpoint(), $lastRequest->getUrl());
        $this->assertEquals($requestParams, $lastRequest->getPostFields()->toArray());
    }

    /**
     * @return array
     */
    private function getRequestParams()
    {
        // TODO capture time as an injectable dependency
        $payload = [
            'scope' => $this->scope->getQuerystringParams()['scope'],
            'iss' => $this->clientConfig->getParams()['client_id'],
            'sub' => $this->clientConfig->getParams()['subject'],
            'aud' => $this->getTokenEndpoint(),
            'exp' => time() + TokenRequest::EXPIRES_IN_TIME,
            'nbf' => time() - TokenRequest::NOT_BEFORE_TIME,
            'iat' => time(),
            'jti' => '',
            'typ' => '',
        ];

        $assertion = JWT::encode($payload, $this->clientConfig->getParams()['client_secret']);

        return [
            'grant_type' => TokenRequest::GRANT_TYPE,
            'assertion' => $assertion,
        ];
    }

    private function getTokenEndpoint()
    {
        return vsprintf('%s%s', [
            $this->apiBaseUrl,
            $this->serverConfig->getParams()['token_endpoint'],
        ]);
    }
}
