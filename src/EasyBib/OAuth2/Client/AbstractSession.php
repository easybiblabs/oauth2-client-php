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
     * @var bool
     */
    private $requestsAlreadyMade = false;

    /**
     * @return string
     */
    abstract protected function doGetToken();

    /**
     * @return string
     */
    public function getToken()
    {
        $this->requestsAlreadyMade = true;
        return $this->doGetToken();
    }

    public function reset()
    {
        $this->tokenStore->reset();
    }

    /**
     * @param \EasyBib\OAuth2\Client\TokenStore $tokenStore
     * @throws \LogicException
     */
    public function setTokenStore(TokenStore $tokenStore)
    {
        if ($this->requestsAlreadyMade) {
            throw new \LogicException('Cannot set token store after requests already made');
        }

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
