# Managing a User's Playlists

There are lots of operations involving user's playlists that can be performed. Remember to request the correct [scopes](working-with-scopes.md) beforehand.

## Listing a user's playlists

```php
$playlists = $api->getUserPlaylists('USER_ID', [
    'limit' => 5
]);

foreach ($playlists->items as $playlist) {
    echo '<a href="' . $playlist->external_urls->spotify . '">' . $playlist->name . '</a> <br>';
}
```

## Getting info about a specific playlist

```php
$playlist = $api->getUserPlaylist('USER_ID', 'PLAYLIST_ID');

echo $playlist->name;
```

## Getting all tracks in a playlist

```php
$playlistTracks = $api->getUserPlaylistTracks('USER_ID', 'PLAYLIST_ID');

foreach ($playlistTracks->items as $track) {
    $track = $track->track;

    echo '<a href="' . $track->external_urls->spotify . '">' . $track->name . '</a> <br>';
}
```

## Creating a new playlist

```php
$api->createUserPlaylist('USER_ID', [
    'name' => 'My shiny playlist'
]);
```

## Updating the details of a user's playlist

```php
$api->updateUserPlaylist('USER_ID', 'PLAYLIST_ID', [
    'name' => 'New name'
]);
```

## Adding tracks to a user's playlist

```php
$api->addUserPlaylistTracks('USER_ID', 'PLAYLIST_ID', [
    'TRACK_ID',
    'TRACK_ID'
]);
```

## Delete tracks from a user's playlist

```php
$tracks = [
    ['id' => 'TRACK_ID'],
    ['id' => 'TRACK_ID'],
];

$api->deleteUserPlaylistTracks('USER_ID', 'PLAYLIST_ID', $tracks, 'SNAPSHOT_ID');
```

## Replacing all tracks in a user's playlist with new ones

```php
$api->replaceUserPlaylistTracks('USER_ID', 'PLAYLIST_ID', [
    'TRACK_ID',
    'TRACK_ID'
]);
```

## Reorder the tracks in a user's playlist

```php
$api->reorderUserPlaylistTracks('USER_ID', 'PLAYLIST_ID', [
    'range_start' => 1,
    'range_length' => 5,
    'insert_before' => 10,
    'snapshot_id' => 'SNAPSHOT_ID'
]);
```

Please see the [method reference](/docs/method-reference/SpotifyWebAPI.md) for more available options for each method.
