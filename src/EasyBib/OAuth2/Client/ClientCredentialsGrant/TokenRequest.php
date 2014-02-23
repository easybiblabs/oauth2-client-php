<?php

namespace EasyBib\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\TokenRequestInterface;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;
use Guzzle\Http\ClientInterface;

class TokenRequest implements TokenRequestInterface
{
    const GRANT_TYPE = 'client_credentials';

    /**
     * @var ClientConfig
     */
    private $clientConfig;

    /**
     * @var ServerConfig
     */
    private $serverConfig;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var Scope
     */
    private $scope;

    /**
     * @param ClientConfig $clientConfig
     * @param ServerConfig $serverConfig
     * @param ClientInterface $httpClient
     * @param Scope $scope
     */
    public function __construct(
        ClientConfig $clientConfig,
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
     * @return TokenResponse
     */
    public function send()
    {
        $url = $this->serverConfig->getParams()['token_endpoint'];
        $request = $this->httpClient->post($url, [], $this->getParams());
        $responseBody = $request->send()->getBody(true);

        return new TokenResponse(json_decode($responseBody, true));
    }

    /**
     * @return array
     */
    private function getParams()
    {
        return [
            'grant_type' => TokenRequest::GRANT_TYPE,
            'client_id' => $this->clientConfig->getParams()['client_id'],
            'client_secret' => $this->clientConfig->getParams()['client_secret'],
        ];
    }
}
