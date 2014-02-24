<?php

namespace EasyBib\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\TokenRequestFactoryInterface;
use Guzzle\Http\ClientInterface;

class JsonWebTokenRequestFactory implements TokenRequestFactoryInterface
{
    private $clientConfig;
    private $serverConfig;
    private $httpClient;
    private $scope;
    private $baseTime;

    public function __construct(
        ClientConfig $clientConfig,
        ServerConfig $serverConfig,
        ClientInterface $httpClient,
        Scope $scope,
        $baseTime = null
    ) {
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;
        $this->httpClient = $httpClient;
        $this->scope = $scope;
        $this->baseTime = $baseTime ?: time();
    }

    public function create()
    {
        return new TokenRequest(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope,
            $this->baseTime
        );
    }
}
