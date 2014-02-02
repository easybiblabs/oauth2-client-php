<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization;


use EasyBib\OAuth2\Client\ArrayValidationException;
use EasyBib\OAuth2\Client\ArrayValidator;

class AuthorizationResponse
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private static $validSuccessParams = [
        'code',
        // 'state'  // not yet supported
    ];

    /**
     * @var array
     */
    private static $requiredErrorParams = [
        'error',
    ];

    private static $permittedErrorParams = [
        'error',
        'error_description',
        'error_uri',
        // 'state'  // not yet supported
    ];

    /**
     * @param array $params
     * @throws InvalidAuthorizationResponseException
     */
    public function __construct(array $params)
    {
        $this->params = $params;

        if (!$this->isSuccess() && !$this->isError()) {
            $message = sprintf(
                'Invalid authorization response params: %s',
                json_encode($params)
            );

            throw new InvalidAuthorizationResponseException($message);
        }
    }

    /**
     * @throws AuthorizationErrorException
     * @return string
     */
    public function getCode()
    {
        if ($this->isSuccess()) {
            return $this->params['code'];
        }

        throw new AuthorizationErrorException($this->params['error']);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        $successValidator = new ArrayValidator(self::$validSuccessParams, self::$validSuccessParams);
        return $this->isValidWith($successValidator);
    }

    public function isError()
    {
        $errorValidator = new ArrayValidator(
            self::$requiredErrorParams,
            self::$permittedErrorParams
        );

        return $this->isValidWith($errorValidator);
    }

    /**
     * @param ArrayValidator $validator
     * @return bool
     */
    private function isValidWith(ArrayValidator $validator)
    {
        try {
            $validator->validate($this->params);
            return true;
        } catch (ArrayValidationException $e) {
            return false;
        }
    }
}
