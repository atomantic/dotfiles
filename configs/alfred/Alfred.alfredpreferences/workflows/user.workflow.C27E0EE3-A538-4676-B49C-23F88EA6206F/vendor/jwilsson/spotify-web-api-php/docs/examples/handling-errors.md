# Handling Errors

Whenever the API returns a error of some sort, a [PHP Exception](http://php.net/manual/en/language.exceptions.php) will be thrown.
The `message` property will be set to the error message returned by the Spotify API and the `code` property will be set to the HTTP status code returned by the Spotify API.

```php
try {
    $track = $api->getTrack('non-existing-track');
} catch (Exception $e) {
    echo 'Spotify API Error: ' . $e->getCode(); // Will be 404
}
```

## Handling rate limit errors

```php
try {
    $track = $api->getTrack('7EjyzZcbLxW7PaaLua9Ksb');
} catch (Exception $e) {
    if ($e->getCode() == 429) { // 429 is Too Many Requests
        $lastResponse = $api->getRequest()->getLastResponse(); // Note "getRequest()" since $api->getLastResponse() won't be set

        $retryAfter = $lastResponse['headers']['Retry-After']; // Number of seconds to wait before sending another request
    } else {
        // Some other kind of error
    }
}
```

Read more about the exact mechanics of rate limiting in the [Spotify API docs](https://developer.spotify.com/web-api/user-guide/#rate-limiting).
