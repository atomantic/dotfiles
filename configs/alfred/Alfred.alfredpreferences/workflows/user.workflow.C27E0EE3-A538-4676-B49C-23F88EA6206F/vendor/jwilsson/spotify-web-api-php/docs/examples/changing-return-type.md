# Changing Return Type

When requesting data from Spotify the default return format is an PHP object, sometimes wrapped in an array if there are multiple entries. An example is `SpotifyWebAPI::getArtists()`. However, it's possible to get an associative array instead of an object.

## Changing the return type

```php
$api->setReturnType(SpotifyWebAPI::RETURN_ASSOC);

$user = $api->me(); // Will be an associative array
```

## Checking the current return type

```php
var_dump($api->getReturnType()); // 'assoc'
```

The possible values are:

* `SpotifyWebAPI::RETURN_ASSOC` - Return associative arrays.
* `SpotifyWebAPI::RETURN_OBJECT` - Return objects (default).
