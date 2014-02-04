<?php

namespace EasyBib\OAuth2\Client;

use EasyBib\Guzzle\Plugin\BearerAuth\BearerAuth;
use Guzzle\Http\ClientInterface;

abstract class AbstractSession
{
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @return string
     */
    abstract public function getToken();

    /**
     * @param TokenStore $tokenStore
     */
    abstract public function setTokenStore(TokenStore $tokenStore);

    /**
     * @param Scope $scope
     */
    abstract public function setScope(Scope $scope);

    /**
     * @param ClientInterface $httpClient
     */
    public function addResourceClient(ClientInterface $httpClient)
    {
        $subscriber = new BearerAuth($this);
        $httpClient->addSubscriber($subscriber);
    }
}
