<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\StateStore;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class StateStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var StateStore
     */
    private $stateStore;

    public function setUp()
    {
        parent::setUp();

        $this->session = new Session(new MockArraySessionStorage());
        $this->stateStore = new StateStore($this->session);
    }

    public function testGetState()
    {
        $stateValue = 'ABC123';
        $this->session->set(StateStore::KEY_STATE, $stateValue);

        $this->assertEquals($stateValue, $this->stateStore->getState());
    }

    public function testValidateResponseWhereMatches()
    {
        $stateValue = 'ABC123';
        $this->session->set(StateStore::KEY_STATE, $stateValue);
        $response = $this->getAuthorizationResponse($stateValue);

        $this->assertTrue($this->stateStore->validateResponse($response));
    }

    public function testValidateResponseWhereDoesNotMatch()
    {

    }

    public function testValidateResponseWhereNotInitialized()
    {

    }

    /**
     * @param string $state
     * @return AuthorizationResponse
     */
    private function getAuthorizationResponse($state)
    {
        return new AuthorizationResponse([
            'code'  => 'XYZ987',
            'state' => $state,
        ]);
    }
}
