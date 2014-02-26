# Client Credentials Grant

In this grant type, your client has a privileged ID and password/secret arranged with the
OAuth2 provider. There is no input required by the user in this grant type.

[The spec](http://tools.ietf.org/html/rfc6749#section-4.4) describes two modes
of authenticating a client.

## HTTP Basic client authentication

```php
use EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasic\ClientConfig;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\SimpleSession;
use EasyBib\OAuth2\Client\Scope;
use Guzzle\Http\Client;

class MyWebController
{
    protected $resourceClient;

    private function setUpOAuth()
    {
        // your application's settings for the OAuth2 provider
        $clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'client_password' => 'password_456',
        ]);

        // the OAuth2 provider's settings
        $serverConfig = new ServerConfig([
            'token_endpoint' => '/oauth/token',
        ]);

        $oauthHttpClient = new Client('http://myoauth2provider.example.com');

        $scope = new Scope(['USER_DATA_READ']);

        $tokenRequestFactory = new TokenRequestFactory(
            $clientConfig,
            $serverConfig,
            $oauthHttpClient,
            $scope
        );

        $this->resourceHttpClient = new Client('http://coolresources.example.com');

        $session = new SimpleSession($tokenRequestFactory);
        $session->addResourceClient($this->resourceHttpClient);
    }

    public function fooAction()
    {
        $apiRequest = $this->resourceHttpClient->get('/some/resource');

        // ...
    }
}
```

## Request body parameter client authentication

```php
use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\ClientConfig;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\SimpleSession;
use EasyBib\OAuth2\Client\Scope;
use Guzzle\Http\Client;

class MyWebController
{
    protected $resourceClient;

    private function setUpOAuth()
    {
        // your application's settings for the OAuth2 provider
        $clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'client_secret' => 'secret_456',
        ]);

        // the OAuth2 provider's settings
        $serverConfig = new ServerConfig([
            'token_endpoint' => '/oauth/token',
        ]);

        $oauthHttpClient = new Client('http://myoauth2provider.example.com');

        $scope = new Scope(['USER_DATA_READ']);

        $tokenRequestFactory = new TokenRequestFactory(
            $clientConfig,
            $serverConfig,
            $oauthHttpClient,
            $scope
        );

        $this->resourceHttpClient = new Client('http://coolresources.example.com');

        $session = new SimpleSession($tokenRequestFactory);
        $session->addResourceClient($this->resourceHttpClient);
    }

    public function fooAction()
    {
        $apiRequest = $this->resourceHttpClient->get('/some/resource');

        // ...
    }
}
```
