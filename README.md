# OAuth 2.0 Client for PHP

You can read all about OAuth2 at the
[standard](http://tools.ietf.org/html/rfc6749).

## Grant types

### Supported

* [Authorization Code Grant](http://tools.ietf.org/html/rfc6749#section-4.1)
* [Client Credentials Grant](http://tools.ietf.org/html/rfc6749#section-4.4)
    * [HTTP Basic client authentication](http://tools.ietf.org/html/rfc6749#section-2.3.1)
    * [request body parameter client authentication](http://tools.ietf.org/html/rfc6749#section-2.3.1)
* [JSON Web Token Grant](http://tools.ietf.org/html/draft-ietf-oauth-json-web-token-15)
has minimal support.

### Not supported

* [Implicit Grant](http://tools.ietf.org/html/rfc6749#section-4.2)
* [Resource Owner Password Credentials Grant](http://tools.ietf.org/html/rfc6749#section-4.3)

## Prerequisites

This library requires PHP 5.4 or later.

Use [Composer](https://getcomposer.org/) to add this project to your project's
dependencies.

## Documentation

Further documentation is in [docs](docs/).

## How to contribute

See [Contributing](CONTRIBUTING.md)

## License

This library is licensed under the BSD 2-Clause License. Enjoy!

You can find more about this
license at http://opensource.org/licenses/BSD-2-Clause
