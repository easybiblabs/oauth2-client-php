<?php

namespace EasyBib\OAuth2\Client;

use Guzzle\Http\ClientInterface;

interface SessionInterface
{
    /**
     * @return string
     */
    public function getToken();

    /**
     * @param ClientInterface $client
     */
    public function addResourceClient(ClientInterface $client);

    /**
     * @param TokenStore $tokenStore
     */
    public function setTokenStore(TokenStore $tokenStore);

    /**
     * @param Scope $scope
     */
    public function setScope(Scope $scope);
}
