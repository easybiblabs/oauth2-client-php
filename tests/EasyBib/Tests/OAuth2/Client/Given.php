<?php

namespace EasyBib\Tests\OAuth2\Client;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\TokenStore;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Symfony\Component\HttpFoundation\Session\Session;

class Given
{
    /**
     * @param string $token
     * @param Scope $scope
     * @param \Guzzle\Plugin\Mock\MockPlugin $mockResponses
     */
    public function iAmReadyToRespondToATokenRequest($token, Scope $scope, MockPlugin $mockResponses)
    {
        $params = [
            'access_token' => $token,
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'refresh_token' => 'refresh_XYZ987',
        ] + $scope->getQuerystringParams();

        $tokenData = json_encode($params);

        $rawTokenResponse = new Response(200, [], $tokenData);
        $mockResponses->addResponse($rawTokenResponse);
    }

    /**
     * @param string $token
     * @param Session $session
     */
    public function iHaveATokenInSession($token, Session $session)
    {
        $session->set(TokenStore::KEY_ACCESS_TOKEN, $token);
    }

    /**
     * @param Session $session
     */
    public function myTokenIsExpired(Session $session)
    {
        $session->set(TokenStore::KEY_EXPIRES_AT, time() - 100);
    }

    /**
     * @param Session $session
     * @param int $after
     */
    public function myTokenExpiresLater(Session $session, $after = 100)
    {
        $session->set(TokenStore::KEY_EXPIRES_AT, time() + $after);
    }

    /**
     * @param $refreshToken
     * @param Session $session
     */
    public function iHaveARefreshToken($refreshToken, Session $session)
    {
        $session->set(TokenStore::KEY_REFRESH_TOKEN, $refreshToken);
    }
}
