<?php

namespace EasyBib\OAuth2\Client;

use EasyBib\Guzzle\Plugin\BearerAuth\BearerAuth;
use Guzzle\Http\ClientInterface;

abstract class AbstractSession
{
    /**
     * @var \EasyBib\OAuth2\Client\TokenStore
     */
    protected $tokenStore;

    /**
     * @return string
     */
    abstract public function getToken();

    /**
     * @param \EasyBib\OAuth2\Client\TokenStore $tokenStore
     */
    public function setTokenStore(TokenStore $tokenStore)
    {
        $this->tokenStore = $tokenStore;
    }

    /**
     * @param ClientInterface $httpClient
     */
    public function addResourceClient(ClientInterface $httpClient)
    {
        $subscriber = new BearerAuth($this);
        $httpClient->addSubscriber($subscriber);
    }
}
