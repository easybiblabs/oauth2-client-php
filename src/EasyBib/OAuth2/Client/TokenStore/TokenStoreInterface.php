<?php

namespace EasyBib\OAuth2\Client\TokenStore;

interface TokenStoreInterface
{
    /**
     * @param string $token
     * @return void
     */
    public function setToken($token);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param int $time
     * @return void
     */
    public function setExpirationTime($time);

    /**
     * @return int
     */
    public function getExpirationTime();
}
