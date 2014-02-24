<?php

namespace EasyBib\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\TokenRequestFactoryInterface;
use Guzzle\Http\ClientInterface;

class HttpBasicTokenRequestFactory implements TokenRequestFactoryInterface
{
    private $clientConfig;
    private $serverConfig;
    private $httpClient;
    private $scope;

    public function __construct(
        HttpBasicClientConfig $clientConfig,
        ServerConfig $serverConfig,
        ClientInterface $httpClient,
        Scope $scope
    ) {
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;
        $this->httpClient = $httpClient;
        $this->scope = $scope;
    }

    public function create()
    {
        return new HttpBasicTokenRequest(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope
        );
    }
}
