<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;


use EasyBib\OAuth2\Client\ArrayValidator;

class TokenResponse
{
    private $token;

    private static $requiredParams = [
        'access_token',
    ];

    public function __construct(array $params)
    {
        self::validate($params);
        $this->token = $params['access_token'];
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param array $params
     * @throws \InvalidArgumentException
     */
    private static function validate(array $params)
    {
        $validator = new ArrayValidator(self::$requiredParams);
        $validator->validate($params);
    }
}
