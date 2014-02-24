<?php

namespace EasyBib\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\TokenRequestFactoryInterface;
use Guzzle\Http\ClientInterface;

class HttpBasicTokenRequestFactory implements TokenRequestFactoryInterface
{
    /**
     * @var HttpBasicClientConfig
     */
    private $clientConfig;

    /**
     * @var \EasyBib\OAuth2\Client\ServerConfig
     */
    private $serverConfig;

    /**
     * @var \Guzzle\Http\ClientInterface
     */
    private $httpClient;

    /**
     * @var \EasyBib\OAuth2\Client\Scope
     */
    private $scope;

    /**
     * @param HttpBasicClientConfig $clientConfig
     * @param ServerConfig $serverConfig
     * @param ClientInterface $httpClient
     * @param Scope $scope
     */
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

    /**
     * @return HttpBasicTokenRequest
     */
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
