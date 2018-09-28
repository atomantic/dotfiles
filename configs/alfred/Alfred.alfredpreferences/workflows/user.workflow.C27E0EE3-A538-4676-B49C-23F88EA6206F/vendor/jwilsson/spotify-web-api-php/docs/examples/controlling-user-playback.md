# Controlling User Playback

Using Spotify Connect, it's possible to control the playback of the currently authenticated user.

## Start and stop playback
```php
// With Device ID
$api->play($deviceId, [
    'uris' => ['TRACK_URI'],
]);

// Without Device ID
$api->play(false, [
    'uris' => ['TRACK_URI'],
]);

$api->pause();
```

## Playing the next or previous track
```php
$api->previous();

$api->next();
```

## Move to a specific position in a track
```php
$api->seek([
    'position_ms' => 60000 + 37000, // Move to the 1.37 minute mark
]);
```

## Set repeat and shuffle mode
```php
$api->repeat([
    'state' => 'track',
]);

$api->shuffle([
    'state' => false,
]);
```

## Control the volume
```php
$api->changeVolume([
    'volume_percent' => 78,
]);
```

## Retrying API calls
Sometimes, a API call might return a `202 Accepted` response code. When this occurs, you should retry the request after a few seconds. For example:

    <?php
    try {
        $wasPaused = $api->pause():

        if (!$wasPaused) {
            $lastResponse = $api->getLastResponse();

            if ($lastResponse['status'] == 202) {
                // Perform some logic to retry the request after a few seconds
            }
        }
    } catch (Exception $e) {
        // Handle the error
    }

Read more about working with Spotify Connect in the [Spotify API docs](https://developer.spotify.com/web-api/working-with-connect/).
