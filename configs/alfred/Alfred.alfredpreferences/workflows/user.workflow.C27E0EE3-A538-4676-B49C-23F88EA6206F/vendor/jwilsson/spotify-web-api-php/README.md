# Spotify Web API PHP

[![Packagist](https://img.shields.io/packagist/v/jwilsson/spotify-web-api-php.svg)](https://packagist.org/packages/jwilsson/spotify-web-api-php)
[![Build Status](https://travis-ci.org/jwilsson/spotify-web-api-php.svg?branch=master)](https://travis-ci.org/jwilsson/spotify-web-api-php)
[![Coverage Status](https://coveralls.io/repos/jwilsson/spotify-web-api-php/badge.svg?branch=master)](https://coveralls.io/r/jwilsson/spotify-web-api-php?branch=master)

This is a PHP wrapper for [Spotify's Web API](https://developer.spotify.com/web-api/). It includes the following:

* Helper methods for all API endpoints:
    * Information about artists, albums, tracks, and users.
    * List music featured by Spotify.
    * Playlist and user music library management.
    * Spotify catalog search.
    * User playback control.
* Authorization flow helpers.
* PSR-4 autoloading support.

## Requirements
* PHP 5.5 or greater.
* PHP [cURL extension](http://php.net/manual/en/book.curl.php) (Usually included with PHP).

## Installation
Install it using [Composer](https://getcomposer.org/):

```sh
composer require jwilsson/spotify-web-api-php
```

## Usage
Before using the Spotify Web API, you'll need to create an app at [Spotify’s developer site](https://developer.spotify.com/web-api/).

Simple example displaying a user's profile:
```php
require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    'CLIENT_ID',
    'CLIENT_SECRET',
    'REDIRECT_URI'
);

$api = new SpotifyWebAPI\SpotifyWebAPI();

if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $api->setAccessToken($session->getAccessToken());

    print_r($api->me());
} else {
    $options = [
        'scope' => [
            'user-read-email',
        ],
    ];

    header('Location: ' . $session->getAuthorizeUrl($options));
    die();
}
```

For more instructions and examples, check out the [documentation](/docs/).

The [Spotify Web API Console](https://developer.spotify.com/web-api/console/) can also be of great help when trying out the API.

## Contributing
Contributions are more than welcome! See [CONTRIBUTING.md](/CONTRIBUTING.md) for more info.

## License
MIT license. Please see [LICENSE.md](LICENSE.md) for more info.
