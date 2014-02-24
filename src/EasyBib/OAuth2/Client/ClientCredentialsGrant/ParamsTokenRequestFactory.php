<?php

namespace EasyBib\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\TokenRequestFactoryInterface;
use Guzzle\Http\ClientInterface;

class ParamsTokenRequestFactory implements TokenRequestFactoryInterface
{
    /**
     * @var ParamsClientConfig
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
     * @param ParamsClientConfig $clientConfig
     * @param ServerConfig $serverConfig
     * @param ClientInterface $httpClient
     * @param Scope $scope
     */
    public function __construct(
        ParamsClientConfig $clientConfig,
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
     * @return ParamsTokenRequest
     */
    public function create()
    {
        return new ParamsTokenRequest(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope
        );
    }
}
