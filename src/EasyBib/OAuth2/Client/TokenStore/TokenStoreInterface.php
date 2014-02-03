<?php

namespace EasyBib\OAuth2\Client\TokenStore;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\TokenResponse;

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
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken);

    /**
     * @return string
     */
    public function getRefreshToken();

    /**
     * @param int $time
     * @return void
     */
    public function setExpiresAt($time);

    /**
     * @return int
     */
    public function getExpiresAt();

    /**
     * @param TokenResponse $tokenResponse
     */
    public function updateFromTokenResponse(TokenResponse $tokenResponse);
}
