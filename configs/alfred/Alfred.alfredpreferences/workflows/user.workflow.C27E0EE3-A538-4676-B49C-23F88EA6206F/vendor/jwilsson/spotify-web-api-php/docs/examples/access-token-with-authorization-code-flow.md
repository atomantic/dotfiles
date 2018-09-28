# Authorization Using the Authorization Code Flow

All API methods require authorization. Before using these methods you'll need to create an app at [Spotify's developer site](https://developer.spotify.com/web-api/).

The Authorization Code Flow method requires some interaction from the user but in turn allows access to user information. There are two steps required to authenticate the user. The first step is to request access to the user's account and data (known as *scopes*) and redirecting them to your app's authorize URL.

### Step 1
```php
require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    'CLIENT_ID',
    'CLIENT_SECRET',
    'REDIRECT_URI'
);

$options = [
    'scope' => [
        'playlist-read-private',
        'user-read-private',
    ],
];

header('Location: ' . $session->getAuthorizeUrl($options));
die();
```

To read more about scopes, see [Working with Scopes](/docs/examples/working-with-scopes.md). To see all of the available options for `getAuthorizeUrl()`, refer to the [method reference](/docs/method-reference/Session.md#getauthorizeurl).

### Step 2
When the user has approved your app, Spotify will redirect the user together with a `code` to the specifed redirect URI. You'll need to use this code to request a access token from Spotify and tell the API wrapper about the access token to use, like this:

```php
require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session('CLIENT_ID', 'CLIENT_SECRET', 'REDIRECT_URI');
$api = new SpotifyWebAPI\SpotifyWebAPI();

// Request a access token using the code from Spotify
$session->requestAccessToken($_GET['code']);
$accessToken = $session->getAccessToken();

// Set the access token on the API wrapper
$api->setAccessToken($accessToken);

// The API can now be used!
```

When requesting a access token, a **refresh token** will also be included. This can be used to extend the validity of access tokens.

## Refreshing an access token
Start by fetching the refresh token from the `Session` instance:

```php
$refreshToken = $session->getRefreshToken();
```

When the access token has expired, request a new one using the refresh token:

```php
$session->refreshAccessToken($refreshToken);

$accessToken = $session->getAccessToken();

// Set our new access token on the API wrapper
$api->setAccessToken($accessToken);
```

For more in-depth technical information about the Authorization Code Flow, please refer to the [Spotify Web API documentation](https://developer.spotify.com/web-api/authorization-guide/#authorization_code_flow).
