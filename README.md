# OAuth 2.0 Client for PHP

You can read all about OAuth at the
[standard](http://tools.ietf.org/html/rfc6749).

Currently, only [Authorization Code Grants](http://tools.ietf.org/html/rfc6749#section-4.1)
are supported.

## Installation

This library requires PHP 5.5 or later.

Use [Composer](https://getcomposer.org/) to add this project to your project's
dependencies.

## Token store

In order to keep your user logged in, you will need a persistent token store.
This is implemented using the Session component of the Symfony HTTP Foundation
package. You can use
[that framework](http://symfony.com/doc/current/components/http_foundation/sessions.html)
to implement whatever backend makes sense in the context of your
application.

## Implementing this client in your app

The `Session` class is the heart of this library. It wraps a Guzzle `Client`,
which it uses to communicate with the OAuth server. It also allows you to
attach any number of Guzzle clients as token subscribers, which you will then
use to make requests against resource servers secured with OAuth.

During the initial authorization step of the OAuth transaction, your app will
need to redirect the user to the OAuth server. After authorization, the OAuth
server will redirect your user back to you.

In order for this OAuth client to initiate the redirect, you will need to
implement our RedirectorInterface within the context of your application. That
may be as simple as calling `header()` to send the Location, or it may involve
a call to your web framework.

```php
use EasyBib\OAuth2\Client\RedirectorInterface;

class MyRedirector implements RedirectorInterface
{
    private $controller;

    public function __construct(MyWebController $controller)
    {
        $this->controller = $controller;
    }

    public function redirect($url)
    {
        // does something which eventually calls header() to redirect user
        $this->controller->redirect($url);
    }
}
```

First, instantiate the basic objects and use them to create an OAuth Session.

```php
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ClientConfig;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ServerConfig;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Session;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

class MyWebController
{
    private oauthSession;

    private function setUpOAuth()
    {
        // Symfony uses native PHP sessions as the default backend
        tokenStore = new TokenStore(new SymfonySession());

        $httpClient = new Client('http://myoauth2provider.example.com');
        $redirector = new MyRedirector($this);

        // your application's settings for the OAuth provider
        $clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'redirect_url' => 'http://myapp.example.com/',
        ]);

        // the OAuth provider's settings
        $serverConfig = new ServerConfig([
            'authorization_endpoint' => '/oauth/authorize',
            'token_endpoint' => '/oauth/token',
        ]);

        $this->oauthSession = new Session(
            $tokenStore,
            $httpClient,
            $redirector,
            $clientConfig,
            $serverConfig
        );

        $scope = new Scope(['USER_READ', 'DATA_READ_WRITE']);
        $this->oauthSession->setScope($scope);
    }
}
```

When you are ready to connect to the service secured with OAuth, you will need
to authorize your user.

```php
$this->oauthSession->authorize();
```

The OAuth server will redirect the user back to your application
with the user's token. Your application should handle that request as follows:

```php
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;

class MyWebController
{
    // this is the action which handles the redirect from the OAuth server
    public function actionReceiveAuthorizationResponseFromOAuth()
    {
        $authorizationResponse = new AuthorizationResponse($_GET);
        $this->oauthSession->handleAuthorizationResponse($authorizationResponse);
    }
}
```

At this point you can access the service being provided, via a fresh Guzzle
client. Do **not** reuse the same client you used in setting up the OAuth
connection itself.

```php
$resourceHttpClient = new Client('http://coolresources.example.com');
$this->oauthSession->addResourceHttpClient($resourceHttpClient);
$request = $resourceHttpClient->get('/some/resource');
// etc.
```

A subscriber has been added to the client which
will add the necessary header to subsequent requests:

```
GET /some/resource HTTP/1.1
Authorization: Bearer token_foo_bar_baz
```

## Token expiration and invalidation

This client will automatically handle token renewal when communicating with
OAuth servers which provide a refresh_token.

In the event that the resource server you are communicating with invalidates
the token, e.g. the user logs out, you will need to handle that condition
within your application, as
[the OAuth standard does not specify behavior of the resource server in that case](http://tools.ietf.org/html/rfc6749#section-1.5).

When that situation is detected within your app, call the `authorize` method
again:

```php
$this->oauthSession->authorize();
```

## License

This library is licensed under the BSD 2-Clause License. Enjoy!

You can find more about this
license at http://opensource.org/licenses/BSD-2-Clause
