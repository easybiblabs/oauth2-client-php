<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AbstractSession;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\TokenStore;
use Guzzle\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class StatelessSession extends AbstractSession
{
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
     * @param Scope $scope
     */
    public function setScope(Scope $scope)
    {
        $this->scope = $scope;
    }

    public function authorize()
    {
        $this->redirector->redirect($this->getAuthorizeUrl());
    }

    /**
     * @param AuthorizationResponse $authResponse
     */
    public function handleAuthorizationResponse(AuthorizationResponse $authResponse)
    {
        $tokenRequest = new TokenRequest(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $authResponse
        );

        $tokenResponse = $tokenRequest->send();
        $this->tokenStore->updateFromTokenResponse($tokenResponse);
    }


    /**
     * @return string
     */
    protected function doGetToken()
    {
        $token = $this->tokenStore->getToken();

        if ($token) {
            return $token;
        }

        if ($this->tokenStore->isRefreshable()) {
            return $this->getRefreshedToken();
        }

        // redirects browser
        $this->authorize();
    }

    /**
     * @return string
     */
    private function getRefreshedToken()
    {
        $refreshRequest = new TokenRefreshRequest(
            $this->tokenStore->getRefreshToken(),
            $this->serverConfig,
            $this->httpClient
        );

        $tokenResponse = $refreshRequest->send();
        $this->tokenStore->updateFromTokenResponse($tokenResponse);

        return $tokenResponse->getToken();
    }

    /**
     * @return string
     */
    private function getAuthorizeUrl()
    {
        $params = ['response_type' => 'code'] + $this->clientConfig->getParams();

        if ($this->scope) {
            $params += $this->scope->getQuerystringParams();
        }

        return vsprintf('%s%s%s%s', [
            $this->httpClient->getBaseUrl(),
            $this->serverConfig->getParams()['authorization_endpoint'],
            '?',
            http_build_query($params),
        ]);
    }
}
