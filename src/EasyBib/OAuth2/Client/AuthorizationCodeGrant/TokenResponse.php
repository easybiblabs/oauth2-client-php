<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\ArrayValidator;

class TokenResponse
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private static $requiredParams = [
        'access_token',
        'token_type',
    ];

    /**
     * @var array
     */
    private static $requiredErrorParams = [
        'error',
    ];

    /**
     * @var array
     */
    private static $permittedErrorParams = [
        'error',
        'error_description',
        'error_uri',
    ];

    /**
     * @param array $params
     * @throws InvalidTokenResponseException
     */
    public function __construct(array $params)
    {
        $this->params = $params;

        if (!$this->isSuccess() && !$this->isError()) {
            throw new InvalidTokenResponseException();
        }
    }

    /**
     * @throws TokenResponseErrorException
     * @return string
     */
    public function getToken()
    {
        if ($this->isError()) {
            throw new TokenResponseErrorException($this->params['error']);
        }

        return $this->params['access_token'];
    }

    /**
     * @return bool
     */
    private function isSuccess()
    {
        $validator = new ArrayValidator(self::$requiredParams);
        return $validator->validate($this->params);
    }

    /**
     * @return bool
     */
    private function isError()
    {
        $validator = new ArrayValidator(
            self::$requiredErrorParams,
            self::$permittedErrorParams
        );

        return $validator->validate($this->params);
    }
}
