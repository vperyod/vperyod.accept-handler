# vperyod.accept-handler
[Aura\Accept] Content Negotiation Middleware

[![Latest version][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

## Installation
```
composer require vperyod/accept-handler
```

## Usage
See [Aura\Accept] documentation.
```php
// Create handler
$handler = new Vperyod\AcceptHandler\AcceptHandler();

// Optionally set the attribute on which to store the `Accept` object
// Defaults to 'aura/accept:accept'
$handler->setAcceptAttribute('accept');

// Add to your middleware stack, radar, relay, etc.
$stack->middleware($handler);

// Subsequent dealings with `Request` will have the `Accept` instance available
// at the previous specified atribute
$accept = $request->getAttribute('accept');


// The `AcceptRequestAwareTrait` should make dealings easier.
//
// Have all your objects that deal with the accept attribute on the request use
// the `AcceptRequestAwareTrait` and have your DI container use the setter, so that 
// they all know where the Accept object is stored.
//
// Additionally, the trait supplies negotiate methods to eaily access the the
// `Accept` Negotiation methods.

class MyResponder
{
    use \Vperyod\AcceptHandler\AcceptRequestAwareTrait;

    protected $availableLangs = [
        //...
    ];

    protected $availableCharset = [
        //...
    ];

    protected $availableMedia = [
        //...
    ];

    public function __invoke($request, $response, $payload)
    {
        // get the accept object
        $accept = $this->getAccept($request);

        // or more convieniant methods
        $language = $this->negotiateLanguage($request, $this->availableLangs);
        $charset = $this->negotiateCharset($request, $this->availableCharset)
        $media = $this->negotiateMedia($request, $this->availableMedia);
        //...
    }
}
```
[Aura\Accept]: https://github.com/auraphp/Aura.Accept

[ico-version]: https://img.shields.io/packagist/v/vperyod/accept-handler.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/vperyod/vperyod.accept-handler/develop.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/vperyod/vperyod.accept-handler.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/vperyod/vperyod.accept-handler.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/vperyod/accept-handler
[link-travis]: https://travis-ci.org/vperyod/vperyod.accept-handler
[link-scrutinizer]: https://scrutinizer-ci.com/g/vperyod/vperyod.accept-handler
[link-code-quality]: https://scrutinizer-ci.com/g/vperyod/vperyod.accept-handler
