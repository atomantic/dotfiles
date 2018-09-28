# Fetching Catalog Information

There are a lot of information about the music on Spotify that can be retrieved. Everything from info about a single track to an artist's top tracks in each country.

```php
require 'vendor/autoload.php';

$api = new SpotifyWebAPI\SpotifyWebAPI();
$track = $api->getTrack('TRACK_ID');

echo '<b>' . $track->name . '</b> by <b>' . $track->artists[0]->name . '</b>';
```

Fetching artists or albums is extremely similar, just change `getTrack` to `getArtist` or `getAlbum`.

## Fetching multiple objects

```php
require 'vendor/autoload.php';

$api = new SpotifyWebAPI\SpotifyWebAPI();
$artists = $api->getArtists([
    'ARTIST_ID',
    'ARTIST_ID',
]);

foreach ($artists->artists as $artist) {
    echo '<b>' . $artist->name . '</b> <br>';
}
```

Of course, `getAlbums` and `getTracks` also exist and work in the same way.

## Getting all tracks on an album

```php
$tracks = $api->getAlbumTracks('ALBUM_ID');

foreach ($tracks->items as $track) {
    echo '<b>' . $track->name . '</b> <br>';
}
```

## Getting an artist's albums

```php
$albums = $api->getArtistAlbums('ALBUM_ID');

foreach ($albums->items as $album) {
    echo '<b>' . $album->name . '</b> <br>';
}
```

## Getting an artist's related artists

```php
$artists = $api->getArtistRelatedArtists('ARTIST_ID');

foreach ($artists->artists as $artist) {
    echo '<b>' . $artist->name . '</b> <br>';
}
```

## Getting an artist’s top tracks

```php
$tracks = $api->getArtistTopTracks('ARTIST_ID', [
    'country' => 'se',
]);

foreach ($tracks->tracks as $track) {
    echo '<b>' . $track->name . '</b> <br>';
}
```

## Getting the audio analysis of a track

```php
$analysis = $api->getAudioAnalysis('TRACK_ID');

print_r($analysis);
```

## Getting recommendations based on artists, tracks, or genres

```php
$seedGenres = $api->getGenreSeeds();

$recommendations = $api->getRecommendations([
    'seed_genres' => $seedGenres->dance,
]);

print_r($recommendations);
```

Please see the [method reference](/docs/method-reference/SpotifyWebAPI.md) for more available options for each method.
