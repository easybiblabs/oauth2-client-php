<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;


use EasyBib\OAuth2\Client\ArrayValidator;

class ServerConfig
{
    /**
     * @var array
     */
    private $params;

    private static $validParams = [
        'authorization_endpoint',
        'token_endpoint',
    ];

    public function __construct(array $params)
    {
        self::validate($params);
        $this->params = $params;
    }

    public function getParams()
    {
        $params = $this->params;

        return $params;
    }

    private static function validate(array $params)
    {
        $validator = new ArrayValidator(self::$validParams, self::$validParams);
        $validator->validate($params);
    }
}
