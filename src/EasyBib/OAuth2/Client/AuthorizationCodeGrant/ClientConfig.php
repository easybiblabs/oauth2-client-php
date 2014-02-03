<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;


use EasyBib\OAuth2\Client\ArrayValidator;

class ClientConfig
{
    /**
     * @var array
     */
    private $params;

    private static $requiredParams = [
        'client_id',
    ];

    private static $permittedParams = [
        'client_id',
        'redirect_url',
        // 'state',  // not yet supported
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
        $validator = new ArrayValidator(self::$requiredParams, self::$permittedParams);

        if (!$validator->validate($params)) {
            throw new InvalidClientConfigException();
        }
    }
}
