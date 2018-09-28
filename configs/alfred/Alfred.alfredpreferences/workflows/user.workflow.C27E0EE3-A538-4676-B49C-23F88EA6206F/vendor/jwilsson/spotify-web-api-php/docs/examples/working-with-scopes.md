# Working with Scopes

All operations involving a user or their information, requires your app to request one or more scopes. You request scopes at the same time as the access token, for example:

```php
require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    'CLIENT_ID',
    'CLIENT_SECRET',
    'REDIRECT_URI'
);

$options = [
    'scope' => [
        'user-library-modify',
        'user-read-birthdate',
    ],
];

header('Location: ' . $session->getAuthorizeUrl($options));
die();
```

It's possible to request more scopes at any time, just add the new ones to the list. This will ask the user to approve your app again.

Please refer to the Spotify docs for a full list of [all available scopes](https://developer.spotify.com/web-api/using-scopes/).
