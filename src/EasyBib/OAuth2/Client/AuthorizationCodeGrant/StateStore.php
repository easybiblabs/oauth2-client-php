<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class StateStore
{
    const KEY_STATE = 'oauth/state';
    const STATE_STRING_LENGTH = 30;

    /**
     * This is a persistent store for state data, which does not necessarily
     * strictly correspond to a user's PHP session
     *
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function getState()
    {
        if ($state = $this->get(self::KEY_STATE)) {
            return $state;
        }

        $state = $this->generateState();
        $this->session->set(self::KEY_STATE, $state);

        return $state;
    }

    /**
     * @param AuthorizationResponse $response
     * @return bool
     * @throws \LogicException
     */
    public function validateResponse(AuthorizationResponse $response)
    {
        if (!$this->isInitiated()) {
            throw new \LogicException('State not initiated');
        }

        if (empty($response->getParams()['state'])) {
            return false;
        }

        return $response->getParams()['state'] == $this->getState();
    }

    /**
     * @return bool
     */
    private function isInitiated()
    {
        return (bool) $this->get(self::KEY_STATE);
    }

    private function generateState()
    {
        $chars = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $numChars = count($chars);

        $string = '';

        for ($i = 0; $i < self::STATE_STRING_LENGTH; $i++) {
            $string .= $chars[rand(0, $numChars-1)];
        }

        return $string;
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function get($name)
    {
        return $this->session->get($name);
    }
}
