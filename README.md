# OAuth2 Client for PHP

You can read all about OAuth2 at the
[standard site](http://tools.ietf.org/html/rfc6749).

## Installation

This library requires PHP 5.5 or later.

Use [Composer](https://getcomposer.org/) to add this project to your project's
dependencies.

Currently, only [Authorization Code Grants](http://tools.ietf.org/html/rfc6749#section-4.1)
are supported.

## Extension for your app

In order to use this client, you will need to implement several interfaces at
key integration points.

### Token storage

Tokens might be stored in session or in a database. For a session implementation,
you might use something like the following:

```php
use EasyBib\OAuth2\Client\TokenStore\TokenStoreInterface;

class SessionTokenStore implements TokenStoreInterface
{
    private $sessionWrapper;

    public function __constructor(MySessionWrapper $sessionWrapper)
    {
        $this->sessionWrapper = $sessionWrapper;
    }

    public function getToken()
    {
        return $this->sessionWrapper->get('easybib.api.token');
    }

    public function setToken($token)
    {
        $this->sessionWrapper->set('easybib.api.token', $token);
    }

    public function setExpirationTime($time)
    {
        $this->sessionWrapper->expireAt($time);
    }
}
```

### Redirection

To make the initial authorization call, your app must redirect the user's
browser to EasyBib's authorization page for confirmation. Your application's
redirect mechanism must be injected via something like this:

```php
use EasyBib\OAuth2\Client\RedirectorInterface;

class MyRedirector implements RedirectorInterface
{
    private $responseWrapper;

    public function __construct(MyResponseWrapper $responseWrapper)
    {
        $this->responseWrapper = $responseWrapper;
    }

    public function redirect($url)
    {
        // throws exception or calls header() to redirect user
        $this->responseWrapper->redirect($url);
    }
}
```

## Usage

First, instantiate the basic objects and use them to create an OAuth Session.

```php
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ClientConfig;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ServerConfig;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Session;

$tokenStore = new SessionTokenStore($session);
$httpClient = new Client('http://myoauth2provider.example.com');
$redirector = new MyRedirector($response);

$clientConfig = new ClientConfig([
    'client_id' => 'client_123',
    'redirect_url' => 'http://myapp.example.com/',
]);

$serverConfig = new ServerConfig([
    'authorization_endpoint' => '/oauth/authorize',
    'token_endpoint' => '/oauth/token',
]);

$session = new Session(
    $tokenStore,
    $httpClient,
    $redirector,
    $clientConfig,
    $serverConfig
);
```

When you are ready to connect to the service providing OAuth2, you will need to authorize
your user.

> TODO fill me in

The OAuth2 server will redirect the user back to your application
with the user's token. Your application should handle that request as follows:

> TODO fill me in

At this point you can access the service being provided, by including with each
request an Authorization header containing the token you received:

```
GET /some/resource HTTP/1.1
Authorization: Bearer token_foo_bar_baz
```

## License

This library is licensed under the BSD 2-Clause License. Enjoy!

You can find more about this
license at http://opensource.org/licenses/BSD-2-Clause
