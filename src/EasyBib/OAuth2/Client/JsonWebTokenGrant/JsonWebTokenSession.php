<?php

namespace EasyBib\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\Guzzle\Plugin\BearerAuth\BearerAuth;
use EasyBib\OAuth2\Client\RedirectorInterface;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\SessionInterface;
use EasyBib\OAuth2\Client\TokenStore;
use Guzzle\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class JsonWebTokenSession implements SessionInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RedirectorInterface
     */
    private $redirector;

    /**
     * @var ClientConfig
     */
    private $clientConfig;

    /**
     * @var ServerConfig
     */
    private $serverConfig;

    /**
     * @var TokenStore
     */
    private $tokenStore;

    /**
     * @var Scope
     */
    private $scope;

    /**
     * @param ClientInterface $httpClient
     * @param RedirectorInterface $redirector
     * @param ClientConfig $clientConfig
     * @param ServerConfig $serverConfig
     */
    public function __construct(
        ClientInterface $httpClient,
        RedirectorInterface $redirector,
        ClientConfig $clientConfig,
        ServerConfig $serverConfig
    ) {
        $this->httpClient = $httpClient;
        $this->redirector = $redirector;
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;

        $this->tokenStore = new TokenStore(new Session());
    }

    /**
     * @return string
     */
    public function getToken()
    {
        $token = $this->tokenStore->getToken();

        if ($token) {
            return $token;
        }

        $this->retrieveToken();

        return $this->tokenStore->getToken();
    }

    /**
     * @param TokenStore $tokenStore
     */
    public function setTokenStore(TokenStore $tokenStore)
    {
        $this->tokenStore = $tokenStore;
    }

    public function setScope(Scope $scope)
    {
        $this->scope = $scope;
    }

    /**
     * @todo this is duplicate code with AuthorizationCodeSession...
     * @param ClientInterface $httpClient
     */
    public function addResourceClient(ClientInterface $httpClient)
    {
        $subscriber = new BearerAuth($this);
        $httpClient->addSubscriber($subscriber);
    }

    /**
     * @return string
     */
    private function retrieveToken()
    {
        $tokenRequest = new TokenRequest(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient
        );

        $tokenResponse = $tokenRequest->send();
        $this->tokenStore->updateFromTokenResponse($tokenResponse);
    }
}
