<?php

namespace EasyBib\OAuth2\Client;

class ArrayValidator
{
    /**
     * @var array
     */
    private $requiredKeys;

    /**
     * @var array
     */
    private $permittedKeys;

    /**
     * @param array $requiredKeys
     * @param array $permittedKeys An optional whitelist for array keys
     */
    public function __construct(array $requiredKeys, array $permittedKeys = null)
    {
        $this->requiredKeys = $requiredKeys;
        $this->permittedKeys = $permittedKeys;
    }

    /**
     * @param array $params
     * @throws ArrayValidationException
     */
    public function validate(array $params)
    {
        if ($missingKeys = array_diff($this->requiredKeys, array_keys($params))) {
            throw new ArrayValidationException('Missing key(s) ' . implode(', ', $missingKeys));
        }

        if (!$this->permittedKeys) {
            return;
        }

        if ($unexpectedKeys = array_diff(array_keys($params), $this->permittedKeys)) {
            throw new ArrayValidationException('Unexpected key(s) ' . implode(', ', $unexpectedKeys));
        }
    }
}
