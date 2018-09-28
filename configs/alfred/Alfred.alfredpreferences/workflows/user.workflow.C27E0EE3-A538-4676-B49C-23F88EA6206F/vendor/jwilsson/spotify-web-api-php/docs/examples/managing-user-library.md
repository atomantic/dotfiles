# Managing a User's Library

There are lots of operations involving a user's library that can be performed. Remember to request the correct [scopes](working-with-scopes.md) beforehand.

## Listing the tracks in a user's library

```php
$tracks = $api->getMySavedTracks([
    'limit' => 5,
]);

foreach ($tracks->items as $track) {
    $track = $track->track;

    echo '<a href="' . $track->external_urls->spotify . '">' . $track->name . '</a> <br>';
}
```

It's also possible to list the albums in a user's library using `getMySavedAlbums`.

## Adding tracks to a user's library

```php
$api->addMyTracks([
    'TRACK_ID',
    'TRACK_ID',
]);
```

It's also possible to add a whole album to a user's library using `addMyAlbums`.

## Deleting tracks from a user's library

```php
$api->deleteMyTracks([
    'TRACK_ID',
    'TRACK_ID',
]);
```

It's also possible to delete a whole album from a user's library using `deleteMyAlbums`.

## Checking if tracks are present in a user's library

```php
$contains = $api->myTracksContains([
    'TRACK_ID',
    'TRACK_ID',
]);

var_dump($contains);
```

It's also possible to check if a whole album is present in a user's library using `myAlbumsContains`.

Please see the [method reference](/docs/method-reference/SpotifyWebAPI.md) for more available options for each method.
