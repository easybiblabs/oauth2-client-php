<?php

namespace EasyBib\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\TokenRequestInterface;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;
use Guzzle\Http\ClientInterface;
use JWT;

class TokenRequest implements TokenRequestInterface
{
    const GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';
    const EXPIRES_IN_TIME = 36000;
    const NOT_BEFORE_TIME = 96000;

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
     * @param ClientConfig $clientConfig
     * @param ServerConfig $serverConfig
     * @param ClientInterface $httpClient
     */
    public function __construct(
        ClientConfig $clientConfig,
        ServerConfig $serverConfig,
        ClientInterface $httpClient
    ) {
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;
        $this->httpClient = $httpClient;
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
        $payload = [
            'scope' => ['USER_READ'],
            'iss' => $this->clientConfig->getParams()['client_id'],
            'sub' => $this->clientConfig->getParams()['subject'],
            'aud' => $this->getTokenEndpoint(),
            'exp' => time() + TokenRequest::EXPIRES_IN_TIME,
            'nbf' => time() - TokenRequest::NOT_BEFORE_TIME,
            'iat' => time(),
            'jti' => '',
            'typ' => '',
        ];

        $assertion = JWT::encode($payload, $this->clientConfig->getParams()['client_secret']);

        return [
            'grant_type' => TokenRequest::GRANT_TYPE,
            'assertion' => $assertion,
        ];
    }

    private function getTokenEndpoint()
    {
        return vsprintf('%s%s', [
            $this->httpClient->getBaseUrl(),
            $this->serverConfig->getParams()['token_endpoint'],
        ]);
    }
}