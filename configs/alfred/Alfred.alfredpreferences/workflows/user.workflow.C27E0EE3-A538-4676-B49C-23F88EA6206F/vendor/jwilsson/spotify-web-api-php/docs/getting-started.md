# Getting Started

## Requirements
* PHP 5.5 or greater.
* PHP [cURL extension](http://php.net/manual/en/book.curl.php) (Usually included with PHP).

## Autoloading
The Spotify Web API for PHP is compatible with [PSR-4](http://www.php-fig.org/psr/psr-4/). This means that the code makes heavy use of namespaces and the correct files can be loaded automatically. All examples throughout this documentation will assume the use of a PSR-4 compatible autoloader, for example via [Composer](https://getcomposer.org/).

## Installation

### Installation via Composer
This is the preferred way of installing the Spotify Web API for PHP. Run the following command in the root of your project:

```sh
composer require jwilsson/spotify-web-api-php
```

Then, in every file where you wish to use the Spotify Web API for PHP, include the following line:

```php
require_once 'vendor/autoload.php';
```

### Manual installation

Download the latest release from the [releases page](https://github.com/jwilsson/spotify-web-api-php/releases). Unzip the files somewhere in your project and include a [PSR-4 compatible autoloader](http://www.php-fig.org/psr/psr-4/examples/) in your project.

## Configuration and setup
First off, make sure you've created an app on [Spotify's developer site](https://developer.spotify.com/web-api/).

Now, before sending requests to Spotify, we need to create a session using your app info:

```php
$session = new SpotifyWebAPI\Session(
    'CLIENT_ID',
    'CLIENT_SECRET',
    'REDIRECT_URI'
);
```

Replace the values here with the ones given to you from Spotify.

## Authentication and authorization
After creating a session it's time to request access to the Spotify Web API. There are two ways to request an access token. The first method is called the *Authorization Code Flow* and requires some interaction from the user, but in turn gives you some access to the user's account. The other method is called the *Client Credentials Flow* and doesn't require any user interaction but doesn't provide any user information. This method is the recommended one if you just need access to Spotify catalog data.

### Requesting an access token using the Authorization Code Flow
There are two steps required to authenticate the user. The first step is for your app to request access to the user's account and then redirecting them to the authorize URL.

#### Step 1
Put the following code in its own file:

```php
require_once 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    'CLIENT_ID',
    'CLIENT_SECRET',
    'REDIRECT_URI'
);

header('Location: ' . $session->getAuthorizeUrl());
die();
```

#### Step 2
When the user has approved your app, Spotify will redirect the user together with a `code` to the specifed redirect URI. This must match the one you entered when you created your app!

You'll need to use this code to request a access token from Spotify and then tell the API wrapper about the access token to use.

Create a new file, and put the following code in it:

```php
require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    'CLIENT_ID',
    'CLIENT_SECRET',
    'REDIRECT_URI'
);

// Request a access token using the code from Spotify
$session->requestAccessToken($_GET['code']);

$accessToken = $session->getAccessToken();

// Store the access token somewhere. In a database for example.

header('Location: some-other-file.php');
die();
```

After this step is completed, the user will be authenticated and you'll have a access token that's valid for approximately one hour. Read on to find out how to make requests to the API!

### Requesting an access token using the Client Credentials Flow
The second method doesn't require any user interaction and no access to user information is therefore granted.

```php
require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    'CLIENT_ID',
    'CLIENT_SECRET'
);

$session->requestCredentialsToken();
$accessToken = $session->getAccessToken();

// Store the access token somewhere. In a database for example.

header('Location: some-other-file.php');
die();
```

You'll notice the missing redirect URI when initializing the `Session`. When using the Client Credentials Flow, it isn't needed and can simply be omitted from the constructor call.

## Making requests to the Spotify API
Once you have a access token, it's time to start making some requests to the API!

```php
require 'vendor/autoload.php';

// Fetch your access token from somewhere. A database for example.

$api = new SpotifyWebAPI\SpotifyWebAPI();
$api->setAccessToken($accessToken);

print_r(
    $api->getTrack('4uLU6hMCjMI75M1A2tKUQC')
);
```

Congratulations! You now know how to use the Spotify Web API for PHP. The next step is to check out [some examples](/docs/examples/) and the [method reference](/docs/method-reference/).
