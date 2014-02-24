<?php

namespace EasyBib\OAuth2\Client;

class Scope
{
    /**
     * @var array string[]
     */
    private $scopes;

    /**
     * @param array string[] $scopes
     */
    public function __construct(array $scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @return array
     */
    public function getQuerystringParams()
    {
        if (!$this->scopes) {
            return [];
        }

        return ['scope' => implode(' ', $this->scopes)];
    }
}
