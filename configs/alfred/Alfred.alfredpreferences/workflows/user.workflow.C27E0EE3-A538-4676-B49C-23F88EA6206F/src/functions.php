<?php

require_once './src/workflows.php';
require './vendor/autoload.php';

/**
 * isUserPremiumSubscriber function.
 *
 * @param mixed $w
 */
 function isUserPremiumSubscriber($w)
 {
     try {
         $api = getSpotifyWebAPI($w);
         $me = $api->me();
     } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
         return false;
     }
 
     if (isset($me->product)) {
        if($me->product == 'premium') {
            return true;
        }
     }
 
     return false;
 }

/**
 * getArtistName function.
 *
 * @param mixed $w
 * @param mixed $artist_uri
 */
 function getArtistName($w, $artist_uri)
 {
     try {
         $api = getSpotifyWebAPI($w);
         $artist = $api->getArtist($artist_uri);

        return $artist->name;
     } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
         logMsg('Error(getArtistName): (exception '.print_r($e).')');
 
         return '';
     }
 }

/**
 * getAlbumName function.
 *
 * @param mixed $w
 * @param mixed $album_uri
 */
 function getAlbumName($w, $album_uri)
 {
     try {
         $api = getSpotifyWebAPI($w);
         $album = $api->getAlbum($album_uri);

        return $album->name;
     } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
         logMsg('Error(getAlbumName): (exception '.print_r($e).')');
 
         return '';
     }
 }

/**
 * getPlaylistName function.
 *
 * @param mixed $w
 * @param mixed $playlist_uri
 */
 function getPlaylistName($w, $playlist_uri)
 {
     $url = '';
     $tmp = explode(':', $playlist_uri);
     try {
         $api = getSpotifyWebAPI($w);
         $playlist = $api->getUserPlaylist(urlencode($tmp[2]), $tmp[4], array(
                 'fields' => array(
                     'name',
                 ),
             ));

        return $playlist->name;
     } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
         logMsg('Error(getPlaylistName): (exception '.print_r($e).')');
 
         return '';
     }
 }

/**
 * setRepeatStateSpotifyConnect function.
 *
 * @param mixed $w
 */
 function setRepeatStateSpotifyConnect($w, $device_id, $state)
 {
    if($state) {
        $repeat_state = 'context';
    } else {
        $repeat_state = 'off';
    }
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);
            $api->repeat([
                'state' => $repeat_state,
                'device_id' => $device_id,
            ]);
            $retry = false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(setRepeatStateSpotifyConnect): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
        }
    }
 }

/**
 * isRepeatStateSpotifyConnectActive function.
 *
 * @param mixed $w
 */
 function isRepeatStateSpotifyConnectActive($w)
 {
    // Read settings from JSON

    $settings = getSettings($w);
    
    $country_code = $settings->country_code;

    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);

            $playback_info = $api->getMyCurrentPlaybackInfo(array(
            'market' => $country_code,
            ));
            $retry = false;

            if($playback_info->repeat_state == 'track') {
                return true;
            } else if($playback_info->repeat_state == 'context') {
                return true;
            }
            return false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(isRepeatStateSpotifyConnectActive): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                $retry = false;

                return false;
            }
        }
    }
 }

 
/**
 * setShuffleStateSpotifyConnect function.
 *
 * @param mixed $w
 */
 function setShuffleStateSpotifyConnect($w, $device_id, $state)
 {
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);
            $api->shuffle([
                'state' => $state,
                'device_id' => $device_id,
            ]);
            $retry = false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(setShuffleStateSpotifyConnect): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
        }
    }
 }

/**
 * getVolumeSpotifyConnect function.
 *
 * @param mixed $w
 */
 function getVolumeSpotifyConnect($w, $device_id)
 {
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);
            
            foreach ($api->getMyDevices()->devices as $device) {
                if ($device->is_active) {
                    $retry = false;
                    return $device->volume_percent;
                }
            }
            $retry = false;
            return false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(getVolumeSpotifyConnect): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
        }
    }
}

/**
 * changeVolumeSpotifyConnect function.
 *
 * @param mixed $w
 */
 function changeVolumeSpotifyConnect($w, $device_id, $volume_percent)
 {
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);
            $api->changeVolume([
                'volume_percent' => $volume_percent,
            ]);
            $retry = false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(changeVolumeSpotifyConnect): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
        }
    }
 }

/**
 * playTrackSpotifyConnect function.
 *
 * @param mixed $w
 */
 function playTrackSpotifyConnect($w, $device_id, $track_uri, $context_uri)
 {
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);

            if ($context_uri != '') {
                if ($track_uri != '') {
                    $offset = [
                        'uri' => $track_uri,
                    ];
                    $options = [
                        'context_uri' => $context_uri,
                        'offset' => $offset,
                    ];
                } else {
                    $options = [
                        'context_uri' => $context_uri,
                    ];
                }
                $api->play($device_id, $options);
                $retry = false;
            } else {
                $uris = array();
                $uris[] = $track_uri;
                $options = [
                    'uris' => $uris
                ];
                $api->play($device_id, $options);
                $retry = false;
            }
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(playTrackSpotifyConnect): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
        }
    }
 }

/**
 * nextTrackSpotifyConnect function.
 *
 * @param mixed $w
 */
 function nextTrackSpotifyConnect($w, $device_id)
 {
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);
            $api->next($device_id);
            $retry = false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(nextTrackSpotifyConnect): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
        }
    }
 }

/**
 * previousTrackSpotifyConnect function.
 *
 * @param mixed $w
 */
 function previousTrackSpotifyConnect($w, $device_id)
 {
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);
            $api->previous($device_id);
            $retry = false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(previousTrackSpotifyConnect): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
        }
    }
 }

/**
 * playpauseSpotifyConnect function.
 *
 * @param mixed $w
 */
 function playpauseSpotifyConnect($w, $device_id, $country_code)
 {
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);

            $playback_info = $api->getMyCurrentPlaybackInfo(array(
            'market' => $country_code,
            ));

            $is_playing = $playback_info->is_playing;
            if ($is_playing) {
                $api->pause($device_id);
            } else {
                $api->play($device_id);
            }
            $retry = false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(playpauseSpotifyConnect): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
        }
    }
 }

/**
 * playSpotifyConnect function.
 *
 * @param mixed $w
 */
 function playSpotifyConnect($w, $device_id)
 {
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);
            $api->play($device_id);
            $retry = false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(playSpotifyConnect): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
        }
    }
 }

/**
 * pauseSpotifyConnect function.
 *
 * @param mixed $w
 */
 function pauseSpotifyConnect($w, $device_id)
 {
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);
            $api->pause($device_id);
            $retry = false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(pauseSpotifyConnect): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
        }
    }
 }

/**
 * getSpotifyConnectCurrentDeviceId function.
 *
 * @param mixed $w
 */
 function getSpotifyConnectCurrentDeviceId($w)
 {
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);

            foreach ($api->getMyDevices()->devices as $device) {
                if ($device->is_active) {
                    $retry = false;
                    return $device->id;
                }
            }
            $retry = false;
            return '';
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            if ($e->getMessage() == 'Permissions missing') {
                $retry = false;
                $w->result(null, serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'reset_oauth_settings' /* other_action */,
                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'The workflow needs more privilages to do this, click to restart authentication', array(
                        'Next time you invoke the workflow, you will have to re-authenticate',
                        'alt' => 'Not Available',
                        'cmd' => 'Not Available',
                        'shift' => 'Not Available',
                        'fn' => 'Not Available',
                        'ctrl' => 'Not Available',
                    ), './images/warning.png', 'yes', null, '');
            } else {
                logMsg('Error(getSpotifyConnectCurrentDeviceId): retry '.$nb_retry.' (exception '.print_r($e).')');
                if ($e->getCode() == 429) { // 429 is Too Many Requests
                    $lastResponse = $api->getRequest()->getLastResponse();
                    $retryAfter = $lastResponse['headers']['Retry-After'];
                    sleep(retryAfter);
                } else if ($e->getCode() == 404) {
                    // skip
                    break;
                } else if ($e->getCode() == 500
                    || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                    // retry
                    if ($nb_retry > 5) {
                        handleSpotifyWebAPIException($w, $e);
                        $retry = false;
    
                        return false;
                    }
                    ++$nb_retry;
                    sleep(5);
                } else {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;
    
                    return false;
                }
            }
        }
    }
 }


/**
 * changeUserDevice function.
 *
 * @param mixed $w
 */
 function changeUserDevice($w, $device_id)
 {
    $options = [
        'device_ids' => $device_id,
        'play' => true
    ];

    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);
            $api->changeMyDevice($options);
            $retry = false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(changeUserDevice): retry '.$nb_retry.' (exception '.print_r($e).')');
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 5) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(5);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
            return false;
        }
    }
 
     return true;
 }

/**
 * isShuffleActive function.
 *
 */
function isShuffleActive($print_output)
{
    $w = new Workflows('com.vdesabou.spotify.mini.player');

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    $country_code = $settings->country_code;

    if ($output_application == 'MOPIDY') {
        $isShuffleEnabled = invokeMopidyMethod($w, 'core.tracklist.get_random', array());
        if ($isShuffleEnabled) {
            $command_output = 'true';
        } else {
            $command_output = 'false';
        }
    } else if($output_application == 'APPLESCRIPT') {
        $command_output = exec("osascript -e '
    tell application \"Spotify\"
    if shuffling enabled is true then
        if shuffling is true then
            return \"true\"
        else
            return \"false\"
        end if
    else
        return \"false\"
    end if
    end tell'");
    } else {
        $retry = true;
        $nb_retry = 0;
        while ($retry) {
            try {
                $api = getSpotifyWebAPI($w);
    
                $playback_info = $api->getMyCurrentPlaybackInfo(array(
                'market' => $country_code,
                ));

                if($playback_info->shuffle_state) {
                    $command_output = 'true';
                } else {
                    $command_output = 'false';
                }
    
                $retry = false;
            } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                logMsg('Error(isShuffleActive): retry '.$nb_retry.' (exception '.print_r($e).')');
                if ($e->getCode() == 429) { // 429 is Too Many Requests
                    $lastResponse = $api->getRequest()->getLastResponse();
                    $retryAfter = $lastResponse['headers']['Retry-After'];
                    sleep(retryAfter);
                } else if ($e->getCode() == 404) {
                    // skip
                    break;
                } else if ($e->getCode() == 500
                    || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                    // retry
                    if ($nb_retry > 5) {
                        $retry = false;
    
                        return false;
                    }
                    ++$nb_retry;
                    sleep(5);
                } else {
                    $retry = false;
    
                    return false;
                }
            }
        }
    }
    if($print_output) {
        echo $command_output;
    }

    return $command_output;
}

/**
 * getUserArtworkURL function.
 *
 * @param mixed $w
 */
function getUserArtworkURL($w, $user_id)
{
    $url = '';
    try {
        $api = getSpotifyWebAPI($w);
        $user = $api->getUser(urlencode($user_id));
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        return $url;
    }

    if (isset($user->images)) {

        if (isset($user->images[0]) && isset($user->images[0]->url)) {
            return $user->images[0]->url;
        }
    }

    return $url;
}

/**
 * getUserArtwork function.
 *
 * @param mixed $w
 */
function getUserArtwork($w, $user_id, $forceFetch = false)
{
    $user_folder = $w->data().'/users/'.$user_id;
    $currentArtwork = $user_folder.'/'.$user_id.'.png';

    if(!$forceFetch) {
        if(file_exists($currentArtwork)) {
            return $currentArtwork;
        }
    }

    $url = getUserArtworkURL($w, $user_id);

    if($url != '') {
        if (!file_exists($user_folder)) {
            return './images/artists.png';
        }

        $fp = fopen($currentArtwork, 'w+');
        $options = array(
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 5,
        );
        $w->request("$url", $options);

        return $currentArtwork;
    } else {
        copy('./images/artists.png', $currentArtwork);
        return './images/artists.png';
    }
}

/**
 * getCurrentUser function.
 *
 * @param mixed $w
 */
function getCurrentUser($w)
{
    $current_user = $w->read('current_user.json');

    if ($current_user == false) {
        // This should only happen once

        $settings = getSettings($w);
        $userid = $settings->userid;

        if($userid == false) {
            return;
        }
        
        $ret = $w->write($userid, 'current_user.json');

        $user_folder = $w->data().'/users/'.$userid;
        if (!file_exists($user_folder)) {
                exec("mkdir -p '".$user_folder."'");

                if (file_exists($w->data().'/library.db')) {
                    rename($w->data().'/library.db', $user_folder.'/library.db');
                    link($user_folder.'/library.db',$w->data().'/library.db');
                }
                if (file_exists($w->data().'/settings.json')) {
                    rename($w->data().'/settings.json', $user_folder.'/settings.json');
                    link($user_folder.'/settings.json',$w->data().'/settings.json');
                }
                if (file_exists($w->data().'/history.json')) {
                    rename($w->data().'/history.json', $user_folder.'/history.json');
                    link($user_folder.'/history.json',$w->data().'/history.json');
                }
        }

        $current_user = $w->read('current_user.json');
    }

    return $current_user;
}

/**
 * switchUser function.
 *
 * @param mixed $w
 */
function switchUser($w, $new_user)
{
    $new_user_folder = $w->data().'/users/'.$new_user;
    
    if (file_exists($w->data().'/library.db')) {
        deleteTheFile($w->data().'/library.db');   
    }
    if (file_exists($new_user_folder.'/library.db')) {
        link($new_user_folder.'/library.db',$w->data().'/library.db');
    }

    if (file_exists($w->data().'/settings.json')) {
        deleteTheFile($w->data().'/settings.json');
    }
    if (file_exists($new_user_folder.'/settings.json')) {
        link($new_user_folder.'/settings.json',$w->data().'/settings.json');
    }

    if (file_exists($w->data().'/history.json')) {
        deleteTheFile($w->data().'/history.json');
    }
    if (file_exists($new_user_folder.'/history.json')) {
        link($new_user_folder.'/history.json',$w->data().'/history.json');
    }

    $ret = $w->write($new_user, 'current_user.json');

    displayNotificationWithArtwork($w, 'Current user is now ' . $new_user, getUserArtwork($w, $new_user, true), 'Switch User');

    return;
}

/**
 * newUser function.
 *
 * @param mixed $w
 */
function newUser($w)
{   
    if (file_exists($w->data().'/library.db')) {
        deleteTheFile($w->data().'/library.db');
    }
    if (file_exists($w->data().'/settings.json')) {
        deleteTheFile($w->data().'/settings.json');
    }
    if (file_exists($w->data().'/history.json')) {
        deleteTheFile($w->data().'/history.json');
    }

    // just delete the file 
    if (file_exists($w->data().'/current_user.json')) {
        deleteTheFile($w->data().'/current_user.json');
    }

    exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini')." $query\"'");

    return;
}

/**
 * listUsers function.
 *
 * @param mixed $w
 */
function listUsers($w)
{ 
    $users_folder = $w->data().'/users/';
    $users = scandir($users_folder);
    // loop on users
    foreach ($users as $user) {
        if ($user == '.' || $user == '..' || $user == '.DS_Store') {
            continue;
        }
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    'SWITCH_USER▹'.$user /* other_settings*/,
                    '' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Switch user to '.$user.'', 'Type enter to validate', getUserArtwork($w, $user), 'yes', null, '');
    }
}
/**
 * getSpotifyWebAPI function.
 *
 * @param mixed $w
 */
function getSpotifyWebAPI($w, $old_api = null)
{
    // Read settings from JSON

    $settings = getSettings($w);
    $oauth_client_id = $settings->oauth_client_id;
    $oauth_client_secret = $settings->oauth_client_secret;
    $oauth_redirect_uri = $settings->oauth_redirect_uri;
    $oauth_access_token = $settings->oauth_access_token;
    $oauth_expires = $settings->oauth_expires;
    $oauth_refresh_token = $settings->oauth_refresh_token;

    if ($old_api == null) {
        // create a new api object
        $session = new SpotifyWebAPI\Session($oauth_client_id, $oauth_client_secret, $oauth_redirect_uri);
        $api = new SpotifyWebAPI\SpotifyWebAPI();
    }

    // Check if refresh token necessary
    // if token validity < 20 minutes
    if (time() - $oauth_expires > 2400) {
        if ($old_api != null) {
            // when refresh needed:
            // create a new api object (even if api not null)
            $session = new SpotifyWebAPI\Session($oauth_client_id, $oauth_client_secret, $oauth_redirect_uri);
            $api = new SpotifyWebAPI\SpotifyWebAPI();
        }
        if ($session->refreshAccessToken($oauth_refresh_token) == true) {
            $oauth_access_token = $session->getAccessToken();
            // Set new token to settings
            $ret = updateSetting($w, 'oauth_access_token', $oauth_access_token);
            if ($ret == false) {
                throw new SpotifyWebAPI\SpotifyWebAPIException('Cannot set oauth_access_token', 100);
            }

            $ret = updateSetting($w, 'oauth_expires', time());
            if ($ret == false) {
                throw new SpotifyWebAPI\SpotifyWebAPIException('Cannot set oauth_expires', 100);
            }
            $api->setAccessToken($oauth_access_token);
        } else {
            throw new SpotifyWebAPI\SpotifyWebAPIException('Token could not be refreshed', 100);
        }
    } else {
        // no need to refresh, the old api is
        // stil valid
        if ($old_api != null) {
            $api = $old_api;
        } else {
            // set the access token for the new api
            $api->setAccessToken($oauth_access_token);
        }
    }

    return $api;
}

/**
 * invokeMopidyMethod function.
 *
 * @param mixed $w
 * @param mixed $method
 * @param mixed $params
 * @param bool  $displayError (default: true)
 */
function invokeMopidyMethod($w, $method, $params, $displayError = true)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $mopidy_server = $settings->mopidy_server;
    $mopidy_port = $settings->mopidy_port;

    exec("curl -s -X POST -H Content-Type:application/json -d '{
  \"method\": \"" .$method.'",
  "jsonrpc": "2.0",
  "params": ' .json_encode($params, JSON_HEX_APOS).",
  \"id\": 1
}' http://" .$mopidy_server.':'.$mopidy_port.'/mopidy/rpc', $retArr, $retVal);

    if ($retVal != 0) {
        if ($displayError) {
            displayNotificationWithArtwork($w, 'Mopidy Exception: returned error '.$retVal, './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' Mopidy Exception: returned error '.$retVal."\"'");
        }

        return false;
    }

    if (isset($retArr[0])) {
        $result = json_decode($retArr[0]);
        if (isset($result->result)) {
            return $result->result;
        }
        if (isset($result->error)) {
            logMsg('ERROR: invokeMopidyMethod() method: '.$method.' params: '.json_encode($params, JSON_HEX_APOS).' exception:'.print_r($result));

            if ($displayError) {
                displayNotificationWithArtwork($w, 'Mopidy Exception: '.htmlspecialchars($result->error->message), './images/warning.png', 'Error!');
                exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' Mopidy Exception: '.htmlspecialchars($result->error->message)."\"'");
            }

            return false;
        }
    } else {
        logMsg('ERROR: empty response from Mopidy method: '.$method.' params: '.json_encode($params, JSON_HEX_APOS));
        displayNotificationWithArtwork($w, 'ERROR: empty response from Mopidy method: '.$method.' params: '.json_encode($params, JSON_HEX_APOS), './images/warning.png');
    }
}

/**
 * switchThemeColor function.
 *
 * @param mixed $w
 */
function switchThemeColor($w,$theme_color)
{
    touch($w->data().'/change_theme_color_in_progress');
    $nb_images_downloaded = 0;
    $nb_images_total = 222;
    $w->write('Change Theme Color to ' . $theme_color . '▹'. 0 .'▹'. $nb_images_total .'▹'.time().'▹'.'starting', 'change_theme_color_in_progress');
    $in_progress_data = $w->read('change_theme_color_in_progress');
    $words = explode('▹', $in_progress_data);

    // Read settings from JSON

    $settings = getSettings($w);
    $output_application = $settings->output_application;

    $imgs = scandir('./images/');

    // replace icons from images directory
    foreach ($imgs as $img) {
        if ($img == '.' || $img == '..'
            || $img == 'alfred-workflow-icon.png') {
            continue;
        }

        $icon_url = 'https://github.com/vdesabou/alfred-spotify-mini-player/raw/master/resources/images_' . $theme_color . '/'.$img;

        $fp = fopen('./images/'.$img, 'w+');
        $options = array(
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 5,
        );

        $w->request("$icon_url", $options);

        ++$nb_images_downloaded;
        if ($nb_images_downloaded % 10 === 0) {
            $w->write('Change Theme Color to ' . $theme_color . '▹'.$nb_images_downloaded.'▹'.$nb_images_total.'▹'.$words[3].'▹'.'Icons', 'change_theme_color_in_progress');
        }
    }

    // check icons from images directory
    $hasError = false;
    foreach ($imgs as $img) {
        if ($img == '.' || $img == '..'
            || $img == 'alfred-workflow-icon.png') {
            continue;
        }

        if (!is_file('./images/'.$img) || (is_file('./images/'.$img) && filesize('./images/'.$img) == 0)) {
            $hasError = true;
            logMsg('Error(switchThemeColor): (failed to load '.$img.')');
        }
    }

    // replace UUID images
    $uuid_imgs = array(
            '0A7E4CC3-BA4A-4DD4-AB4B-E2E8F9DBBE8C' => 'playpause',
            'F0ED23FA-8524-4769-B610-828611958D6A' => 'recent',
            '0B75CF43-8B04-405F-86C3-2FFC59AC4A70' => 'artists',
            '05F86AA1-D3EE-4409-9A58-898B36FFE503' => 'kill',
            '15209065-AB33-44E4-ADFC-BAFC5033762E' => 'numbers',
            '15B503BB-DA3F-4B7B-8E4C-94E968ECDCF2' => 'volmax',
            '16F5C3BF-01EE-493C-9E7B-CC54D482B7A6' => 'playlists',
            '1BA522F3-B2D0-4F36-B86C-738FB3AC55DD' => 'volume_up',
            '1F30DEA9-0A81-4E00-9CF0-E7D086C6B5B0' => 'keyup',
            '2B0C6211-1DD0-4CE6-8082-37957F15CC1D' => 'mute',
            '2B0C8466-4AED-4272-9C10-50F3BCE88043' => 'update',
            '2F1F6369-46C0-483B-816F-3796168AE060' => 'repeating',
            '2FC567E2-E6C5-4A91-B42E-1996532B78C9' => 'albums',
            '303A65BF-8E81-48E8-AA28-E1CA408FDD53' => 'keydown',
            '3040749D-6B5C-4CCD-AF95-AEC0F83B48D9' => 'current_track',
            '3617F927-558D-4F30-B8D1-B7789F863AB0' => 'play_queue',
            '39A1935A-37F5-49FA-A860-BCF7765A8C65' => 'icon',
            '3F85747B-FC44-4B07-AA6E-3645F0CC0DF7' => 'add_to_ap_yourmusic',
            '450AEF8B-BDAA-409C-A0D4-68769545FBCF' => 'keyescape',
            '455BDE70-BCF4-447F-ABFF-C25D8E5B08B6' => 'radio_song',
            '4BE9FB30-9B4F-4E44-861D-D178507A7568' => 'remove_from',
            '4EB5FB5E-6472-4757-A479-B206B6080036' => 'next',
            '552D77E3-1550-4035-AB49-8B708B081EC9' => 'online_artist',
            '5AF7EEEC-2E55-482C-8750-4CE6AD752683' => 'play',
            '5C5C6FFD-4A1B-42C4-AF81-E88A92245DD2' => 'play',
            '5EB4A672-01BC-4DBB-AD39-60EBB0F79A67' => 'add_to_ap_yourmusic',
            '62AA861E-C910-4354-84C9-58A5660365D8' => 'remove_from',
            '66F8A022-163E-42CB-A9E9-B32E8158ACA7' => 'previous',
            '6E9B4F21-F907-4A22-8689-9A146F909454' => 'playpause',
            '78DAB9D1-72A3-4B44-AB3B-D1C71F13DF7A' => 'keyenter',
            '794A76A7-BB98-4A83-92BE-386A56875120' => 'issue',
            '79F70A28-E2D9-4705-81A9-86F3EA8EB47F' => 'alfred_playlist',
            '7FE5D993-C14D-4B94-9479-B361680F1C40' => 'add_to',
            '7FF09231-F068-4EA0-8537-6C1EB608CA5A' => 'volume_down',
            '8339BE15-274E-4A77-A6A0-CEDF30EFD0E5' => 'next',
            '8598A5C9-72B6-4CEF-A498-D6C2ED06DC88' => 'radio_song',
            '873EF61A-BD03-4946-87B0-C7AE5DFC5E5B' => 'debug',
            '89BC46B4-E178-4855-8863-393730814F6E' => 'shuffle',
            'BD255BDB-07A5-4EE9-858F-A58C6207D191' => 'shuffle',
            '8C78472B-13EB-4512-B94D-4BF92867CD92' => 'random',
            '8E4347FE-0FC3-4FF1-AAAF-E0C6CD084BB5' => 'volume_down',
            '8F478980-199B-45B5-AD41-EBA185446705' => 'issue',
            '8F8CF9CC-2B4D-4F8E-A211-C32DD49E84F4' => 'playlists',
            '9661EF24-91C3-44A1-9066-8E9B9817841D' => 'artists',
            '9B2621F0-0190-4ABD-A09F-3DBF85548580' => 'random_album',
            '9E3FE06C-F5F8-41F3-A70F-B92E187EED75' => 'volmid',
            'A0EAB8B1-F034-490F-9534-44ADF572AF4E' => 'uncheck',
            'A0F746BD-C7AF-490F-B4D9-8BAEDDDAEF90' => 'alfred_playlist',
            'E6CEF7D4-CFA9-4608-A188-65B33A602BAF' => 'alfred_playlist',
            '11E4CA98-E51E-45E4-91ED-72B4A1A34283' => 'alfred_playlist',
            'A38DD404-DE03-42C2-B0CB-A37891B6F24D' => 'info',
            'A41190FA-4B23-4908-A4B7-16A14F338C11' => 'repeating',
            'A76C26BD-BA48-4797-839B-BE439FF40846' => 'pause',
            'A8BE6109-BCC0-4A41-9375-C1D2E3A755BD' => 'volmax',
            'AC236315-8CDE-41E3-A9BA-BB59D292FE14' => 'lyrics',
            'B23712E3-4564-4668-BD6D-4D535839CC8C' => 'uncheck',
            'B77F5F98-C065-49A4-BBB1-68ADADDD8E7D' => 'albums',
            'BA289B3E-779E-482F-AFEA-8E1395513365' => 'browse',
            'BC488027-E76B-43A7-B414-C7FAA9CF9995' => 'random',
            'C10A975C-0DAE-4A45-AEAB-4E07CF125703' => 'radio_artist',
            'C588A188-D8D3-450F-9020-5EBB563F6B8A' => 'update',
            'C7CFF014-DBC8-4663-A0AB-219C57A427EE' => 'mute',
            'C93B7A2B-9105-4456-997A-1BCC4EDA5A27' => 'radio_artist',
            'CD9D3654-8137-49B2-80DC-095E97A58E67' => 'previous',
            'CEF36AB9-7CC2-4765-BF84-751E88B69023' => 'debug',
            'D31A5001-4590-418E-9AB0-6183E75E59DE' => 'random_album',
            'D4442911-E17B-49CC-8F7F-EAC1830B11CD' => 'volume_up',
            'D8C53798-B7E2-4A51-AC12-37FEBDB624E0' => 'online_artist',
            'D931B685-B5F8-4BBC-9FCB-D78F9FA0AB66' => 'volume_up',
            'DC403223-17FA-466B-9488-7292DA9D8223' => 'info',
            'DC678CF5-D8B2-4508-A2FA-CB0F0E253108' => 'online',
            'DD0755A4-3C70-467C-A005-11F10E23CEF0' => 'settings',
            'E45DF42A-58A9-4069-A410-EC2BBC8A0575' => 'new_releases',
            'EA03C1F5-912A-4422-B766-2BBC94DC0344' => 'volmid',
            'EBD3FE58-A201-4C3D-A2DD-7CD9A6D50A2E' => 'alfred_playlist',
            'EF574432-0896-4A92-9944-7DA5DD7295DA' => 'biography',
            'F250A59C-0B2D-4A08-9085-9CA0A2FB2DCC' => 'volume_up',
            'F4382654-9318-4849-82E4-550AC235148C' => 'pause',
            'F4F5AC18-3C04-4673-9CC3-E563094C9446' => 'add_to',
            '0EBF4C61-5630-4629-8B4F-AD91D3470760' => 'share',
            'E5BAF801-726E-49C0-ABF2-7AD9F9ECD22A' => 'share',
            'FAA5FC99-7909-45B6-9BF0-7601DBAADC4F' => 'youtube',
            'C323BECC-0183-4562-B817-65624E13B3F3' => 'share',
            'B8D706BB-D6E9-4AE3-B36B-ED6D4B34AD5F' => 'connect',
            'icon' => 'icon',
        );

    foreach ($uuid_imgs as $key => $value) {
        $icon_url = 'https://github.com/vdesabou/alfred-spotify-mini-player/raw/master/resources/images_' . $theme_color . '/'.$value.'@3x.png';

        $fp = fopen('./'.$key.'.png', 'w+');
        $options = array(
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 5,
        );

        $w->request("$icon_url", $options);
        ++$nb_images_downloaded;
        if ($nb_images_downloaded % 5 === 0) {
            $w->write('Change Theme Color to ' . $theme_color . '▹'.$nb_images_downloaded.'▹'.$nb_images_total.'▹'.$words[3].'▹'.'Icons UUID', 'change_theme_color_in_progress');
        }
    }

    // check UUID images
    foreach ($uuid_imgs as $key => $value) {
        if (!is_file('./'.$key.'.png') || (is_file('./'.$key.'.png') && filesize('./'.$key.'.png') == 0)) {
            $hasError = true;
            logMsg('Error(switchThemeColor): (failed to load UUID '.$key.')');
        }
    }

    // replace UUID items images for remote
    $uuid_imgs = array(
            'FDAEECD4-DE40-443C-89F9-B46D5592D8C4' => 'issue',
            'F14D664A-48D5-4232-A6CD-772F3361630B' => 'new_releases',
            'EE84BB33-1412-4F21-8B4D-D09362ECFE7A' => 'volmax',
            'EA114C5E-A6A7-479E-BDB8-17E9C3163B53' => 'current_track',
            'E8BE74B6-2928-4513-A0BC-B88EBF839ABD' => 'mute',
            'E7ECAC10-DDC2-4860-A342-A876756D8812' => 'playpause',
            'DF3FC215-4E94-472D-91B2-9D94A3B8632F' => 'keyenter',
            'D427D63A-3C3A-420D-87D5-46185FE361E3' => 'online_artist',
            'D191C6F2-CF3A-4F3A-AEDB-9D8B03EA7EC9' => 'albums',
            'D51D9C70-68AF-4D63-ABD0-09906D9B1EC9' => 'playlists',
            'CED135C7-B958-4C68-8C47-956FBAA9086A' => 'remove_from',
            'CC39269A-4977-48A1-8427-F9CFC2AE8EED' => 'play',
            'AF86DFA1-E6C7-4C2B-BD00-66614B6CFE81' => 'update',
            'AEC59638-4779-4470-9A6A-E32992498ED2' => 'pause',
            'AE944AAF-0B18-436F-AB21-1B22AA446063' => 'browse',
            'A6374417-A3FC-4360-9913-504F7A21F4F1' => 'radio_artist',
            '3690378D-02F4-4BF9-BC91-975F3739542B' => 'random',
            '2737969D-A8B4-4550-8568-3C926D36DD81' => 'add_to_ap_yourmusic',
            '2375184C-CC97-4763-A846-D2FAB1259FD1' => 'random_album',
            '707403B7-FF4A-4995-99F3-AB2B5F39B34F' => 'debug',
            'E9C9194B-AF5F-4BAD-88AB-DB7AFED380BF' => 'recent',
            '163265DD-5CAB-4D11-B984-F86871709AEE' => 'icon',
            '9045D879-6632-4113-9915-85534EBECBB1' => 'online',
            '5365AF85-EDB1-4789-9ABD-B272A8C96AA0' => 'volume_down',
            '4368A343-21A8-44C7-96F9-4870FA1C2EFB' => 'previous',
            '924A7250-A8D3-4944-BDEF-74B3DD32DC75' => 'volume_up',
            '749D3ABB-38FB-4EFB-9E3D-881C5AF5CAC9' => 'next',
            '577A4640-8D94-4813-9223-B355BE7FE1BD' => 'shuffle',
            '569B0F42-A04A-40B7-9E86-EA1C61EF0AE5' => 'play_queue',
            '26215986-3B9E-4491-A93B-67878CE04EB5' => 'alfred_playlist',
            '89CD46BC-7C18-4D85-A23B-CF5F93273B1A' => 'keyescape',
            '83F461DC-A47F-4407-92C3-BF269BB49953' => 'uncheck',
            '83D8A06D-1B1C-4B90-A00A-5B5E575DF7E8' => 'radio_song',
            '58AF0E83-EC70-42B2-AED5-DB7DF65C043C' => 'info',
            '41AECA11-8410-4CFF-8BAB-51FE6DE283F4' => 'volmid',
            '27BDEB50-2E71-474D-B329-30EBFB7BC663' => 'artists',
            '19E641F3-FE15-4058-9597-255FDDAA4F48' => 'settings',
            '9B2EBB97-E54C-420E-BB2E-20AC87764C68' => 'repeating',
            '8B60A0B6-910F-4B63-A0B1-953D9A99990F' => 'lyrics',
            '6D35BFAD-C96C-4BF8-B76B-5C4BB4313DF9' => 'keydown',
            '5C18A3F0-C5CC-4B8D-B71E-B00B420CA2DC' => 'keyup',
            '4FE5620A-FB79-440E-8633-B8148EE1191E' => 'add_to',
            'C5B9A789-80F3-41BA-9A46-C34DD4CDE050' => 'share',
            '15D6EBE2-6D82-4F2C-A4B3-5949424B4EF9' => 'youtube',
            '28180F27-0728-414D-88F3-76E99A58FA7D' => 'connect',
        );

    foreach ($uuid_imgs as $key => $value) {
        $icon_url = 'https://github.com/vdesabou/alfred-spotify-mini-player/raw/master/resources/images_' . $theme_color . '/'.$value.'@3x.png';

        $fp = fopen('./_remote/images/items/'.$key.'.png', 'w+');
        $options = array(
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 5,
        );

        $w->request("$icon_url", $options);

        ++$nb_images_downloaded;
        if ($nb_images_downloaded % 5 === 0) {
            $w->write('Change Theme Color to ' . $theme_color . '▹'.$nb_images_downloaded.'▹'.$nb_images_total.'▹'.$words[3].'▹'.'Icons remote', 'change_theme_color_in_progress');
        }
    }

    // check UUID images
    foreach ($uuid_imgs as $key => $value) {
        if (!is_file('./_remote/images/items/'.$key.'.png') || (is_file('./_remote/images/items/'.$key.'.png') && filesize('./_remote/images/items/'.$key.'.png') == 0)) {
            $hasError = true;
            logMsg('Error(switchThemeColor): (failed to load UUID items remote '.$key.')');
        }
    }

    // replace UUID pages images for remote
    $uuid_imgs = array(
            '8F6D8768-149A-492E-B88F-DECA4DB283B5' => 'keyenter',
            '31BE3663-05A1-4380-9955-ECBD4E1AD618' => 'next',
            '657FE771-8978-4E40-9AA9-201603AC8B5F' => 'update',
        );

    foreach ($uuid_imgs as $key => $value) {
        $icon_url = 'https://github.com/vdesabou/alfred-spotify-mini-player/raw/master/resources/images_' . $theme_color . '/'.$value.'@3x.png';

        $fp = fopen('./_remote/images/pages/'.$key.'.png', 'w+');
        $options = array(
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 5,
        );

        $w->request("$icon_url", $options);

        ++$nb_images_downloaded;
        if ($nb_images_downloaded % 5 === 0) {
            $w->write('Change Theme Color to ' . $theme_color . '▹'.$nb_images_downloaded.'▹'.$nb_images_total.'▹'.$words[3].'▹'.'Icons UUID remote page', 'change_theme_color_in_progress');
        }
    }

    // check UUID images
    foreach ($uuid_imgs as $key => $value) {
        if (!is_file('./_remote/images/pages/'.$key.'.png') || (is_file('./_remote/images/pages/'.$key.'.png') && filesize('./_remote/images/pages/'.$key.'.png') == 0)) {
            $hasError = true;
            logMsg('Error(switchThemeColor): (failed to load UUID pages remote '.$key.')');
        }
    }

    // Get APP 
    $app_url = 'https://github.com/vdesabou/alfred-spotify-mini-player/raw/master/resources/images_' . $theme_color . '/' . rawurlencode('Spotify Mini Player.app.zip');

    $zip_file = '/tmp/SpotifyMiniPlayer.app.zip';
    $fp = fopen($zip_file, 'w+');
    $options = array(
        CURLOPT_FILE => $fp,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_TIMEOUT => 5,
    );

    $w->request("$app_url", $options);
    ++$nb_images_downloaded;
    $w->write('Change Theme Color to ' . $theme_color . '▹'.$nb_images_downloaded.'▹'.$nb_images_total.'▹'.$words[3].'▹'.'Icons UUID remote page', 'change_theme_color_in_progress');

    if (!is_file($zip_file) || (is_file($zip_file) && filesize($zip_file) == 0)) {
        $hasError = true;
        logMsg('Error(switchThemeColor): (failed to load /tmp/SpotifyMiniPlayer.app.zip for '.$theme_color.')');
    }
	$zip_command = 'unzip '  . $zip_file . ' -d ' . '\'./App/'.$theme_color.'/\'';
	exec($zip_command);
    
    exec('open "'.'./App/'.$theme_color.'/Spotify Mini Player.app'.'"');
    //update settings
    $ret = updateSetting($w, 'theme_color', $theme_color);

    deleteTheFile($w->data().'/change_theme_color_in_progress');
    if (!$hasError) {
        displayNotificationWithArtwork($w, 'All existing icons have been replaced by ' . $theme_color . ' icons', './images/change_theme_color.png', 'Settings');
    } else {
        displayNotificationWithArtwork($w, 'Some icons have not been replaced', './images/warning.png');
    }
}

/**
 * createDebugFile function.
 *
 * @param mixed $w
 */
function createDebugFile($w)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $output_application = $settings->output_application;
    $oauth_client_secret = $settings->oauth_client_secret;
    $oauth_access_token = $settings->oauth_access_token;
    $oauth_refresh_token = $settings->oauth_refresh_token;
    $display_name = $settings->display_name;
    $userid = $settings->userid;
    $theme_color = $settings->theme_color;

    exec('mkdir -p /tmp/spot_mini_debug');
    date_default_timezone_set('UTC');
    $date = date('Y-m-d H:i:s', time());

    $output = "Hi!\n\n";
    $output = $output."I'm a real human who will use his free time to have a look at your problem,\n";
    $output = $output."so please take time to describe your problem in a few lines:\n";

    $output = $output."----------------------------------------------\n";

    $output = $output."\n\n\n\n";

    $output = $output."----------------------------------------------\n";

    $output = $output."\n\n\n";

    $output = $output."-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+\n";
    $output = $output.'Generated: '.$date."\n";

    // check for library update in progress
    if (file_exists($w->data().'/update_library_in_progress')) {
        $output = $output.'Library update in progress: '.'the file'.$w->data()."/update_library_in_progress is present\n";
    }

    // settings.json

    copy($w->data().'/settings.json', '/tmp/spot_mini_debug/settings.json');
    // Remove oAuth values from file that will be uploaded
    updateSetting($w, 'oauth_client_secret', 'xxx', '/tmp/spot_mini_debug/settings.json');
    updateSetting($w, 'oauth_access_token', 'xxx', '/tmp/spot_mini_debug/settings.json');
    updateSetting($w, 'display_name', 'xxx', '/tmp/spot_mini_debug/settings.json');
    $output = $output.'* display_name: '.$display_name."\n\n";
    $output = $output.'* oauth_client_secret: '.$oauth_client_secret."\n\n";
    $output = $output.'* oauth_access_token: '.$oauth_access_token."\n\n";
    $output = $output.'* oauth_refresh_token: '.$oauth_refresh_token."\n\n";

    $output = $output."****\n";

    copyDirectory($w->cache(), '/tmp/spot_mini_debug/cache');

    if (!file_exists($w->data().'/fetch_artworks.db')) {
        $output = $output.'The file '.$w->data()."/fetch_artworks.db is not present\n";
    } else {
        copy($w->data().'/fetch_artworks.db', '/tmp/spot_mini_debug/fetch_artworks.db');
    }

    if (!file_exists($w->data().'/library.db')) {
        $output = $output.'The file '.$w->data()."/library.db is not present\n";
    } else {
        copy($w->data().'/library.db', '/tmp/spot_mini_debug/library.db');
    }

    if (!file_exists($w->data().'/library_new.db')) {
        $output = $output.'The file '.$w->data()."/library_new.db is not present\n";
    } else {
        copy($w->data().'/library_new.db', '/tmp/spot_mini_debug/library_new.db');
    }

    if (!file_exists($w->data().'/library_old.db')) {
        $output = $output.'The file '.$w->data()."/library_old.db is not present\n";
    } else {
        copy($w->data().'/library_old.db', '/tmp/spot_mini_debug/library_old.db');
    }

    if (!file_exists($w->data().'/history.json')) {
        $output = $output.'The file '.$w->data()."/history.json is not present\n";
    } else {
        copy($w->data().'/history.json', '/tmp/spot_mini_debug/history.json');
    }

    if (!file_exists($w->data().'/playqueue.json')) {
        $output = $output.'The file '.$w->data()."/playqueue.json is not present\n";
    } else {
        copy($w->data().'/playqueue.json', '/tmp/spot_mini_debug/playqueue.json');
    }

    if (!file_exists(exec('pwd').'/packal/package.xml')) {
        $output = $output.'The file '.exec('pwd')."/packal/package.xml is not present\n";
    } else {
        copy(exec('pwd').'/packal/package.xml', '/tmp/spot_mini_debug/package.xml');
    }

    if (!file_exists($w->data().'/users')) {
        $output = $output.'The directory '.$w->data()."/users is not present\n";
    }

    $output = $output.exec('uname -a');
    $output = $output."\n";
    $output = $output.exec('sw_vers -productVersion');
    $output = $output."\n";
    $output = $output.exec('sysctl hw.memsize');
    $output = $output."\n";
    $output = $output.'alfred_version:'.getenv('alfred_version');
    $output = $output."\n";
    $output = $output.'alfred_version_build:'.getenv('alfred_version_build');
    $output = $output."\n";
    $output = $output.'alfred_workflow_version:'.getenv('alfred_workflow_version');
    $output = $output."\n";
    $output = $output.'alfred_debug:'.getenv('alfred_debug');
    $output = $output."\n";
    if ($output_application != 'MOPIDY') {
        $output = $output.'Spotify desktop version:'.exec("osascript -e 'tell application \"Spotify\" to version'");
    } else {
        $output = $output.'Mopidy version:'.invokeMopidyMethod($w, 'core.get_version', array(), false);
    }
    $output = $output."\n";


    exec('/usr/bin/xattr "'.'./App/'.$theme_color.'/Spotify Mini Player.app'.'"',$response);
    $output = $output."xattr Spotify Mini Player.app returned: ";
    foreach($response as $line) {  
        $output = $output.$line;
        $output = $output."\n";
    }
    $output = $output."\n";

    exec('cd /tmp;zip -r spot_mini_debug.zip spot_mini_debug');

    $output = $output."****\n";

    $output = $output.exec("curl --upload-file /tmp/spot_mini_debug.zip https://transfer.sh/spot_mini_debug_$userid.zip");

    exec('cd /tmp;rm -rf spot_mini_debug.zip spot_mini_debug');

    exec('echo "'.$output.'" | pbcopy');

    exec("open \"mailto:alfred.spotify.mini.player@gmail.com?subject=Alfred Spotify Mini Player debug file&body=$output\"");
}
/**
 * getCurrentTrackInfoWithMopidy function.
 *
 * @param mixed $w
 * @param bool  $displayError (default: true)
 */
function getCurrentTrackInfoWithMopidy($w, $displayError = true)
{
    $tl_track = invokeMopidyMethod($w, 'core.playback.get_current_track', array(), $displayError);
    if ($tl_track == false) {
        return 'mopidy_stopped';
    }
    $state = invokeMopidyMethod($w, 'core.playback.get_state', array(), $displayError);

    $track_name = '';
    $artist_name = '';
    $album_name = '';
    $track_uri = '';
    $length = 0;

    if (isset($tl_track->name)) {
        $track_name = $tl_track->name;
    }

    if (isset($tl_track->artists) &&
        isset($tl_track->artists[0]) &&
        isset($tl_track->artists[0])) {
        $artist_name = $tl_track->artists[0]->name;
    }

    if (isset($tl_track->album) && isset($tl_track->album->name)) {
        $album_name = $tl_track->album->name;
    }

    if (isset($tl_track->uri)) {
        $track_uri = $tl_track->uri;
    }

    if (isset($tl_track->length)) {
        $length = $tl_track->length;
    }

    return ''.$track_name.'▹'.$artist_name.'▹'.$album_name.'▹'.$state.'▹'.$track_uri.'▹'.$length.'▹'.'0';
}

/**
 * getCurrentTrackInfoWithSpotifyConnect function.
 *
 * @param mixed $w
 * @param bool  $displayError (default: true)
 */
 function getCurrentTrackInfoWithSpotifyConnect($w, $displayError = true)
 {
    // Read settings from JSON
    $settings = getSettings($w);
    $country_code = $settings->country_code;

    $track_name = '';
    $artist_name = '';
    $album_name = '';
    $track_uri = '';
    $length = 0;

    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            $api = getSpotifyWebAPI($w);

            $current_track_info = $api->getMyCurrentTrack(array(
            'market' => $country_code,
            ));

            $retry = false;

            $track_name = $current_track_info->item->name;
            $artist_name = $current_track_info->item->artists[0]->name;
            $album_name = $current_track_info->item->album->name;
            $is_playing = $current_track_info->is_playing;
            if ($is_playing) {
                $state = 'playing';
            } else {
                $state = 'paused';
            }
            $track_uri = $current_track_info->item->uri;
            $length = ($current_track_info->item->duration_ms);
            $popularity = $current_track_info->item->popularity;
        
            $retArr = array(''.$track_name.'▹'.$artist_name.'▹'.$album_name.'▹'.$state.'▹'.$track_uri.'▹'.$length.'▹'.$popularity);
            return ''.$track_name.'▹'.$artist_name.'▹'.$album_name.'▹'.$state.'▹'.$track_uri.'▹'.$length.'▹'.'0';
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503 || $e->getCode() == 202) {
                // retry
                if ($nb_retry > 2) {
                    $retry = false;

                    return 'connect_stopped';
                }
                ++$nb_retry;
                sleep(5);
            } else {
                $retry = false;

                return 'connect_stopped';
            }
            return 'connect_stopped';
        }
    }
 }

/**
 * playUriWithMopidyWithoutClearing function.
 *
 * @param mixed $w
 * @param mixed $uri
 */
function playUriWithMopidyWithoutClearing($w, $uri)
{
    $tl_tracks = invokeMopidyMethod($w, 'core.tracklist.add', array('uris' => array($uri), 'at_position' => 0));
    if (isset($tl_tracks[0])) {
        invokeMopidyMethod($w, 'core.playback.play', array('tl_track' => $tl_tracks[0]));
    } else {
        displayNotificationWithArtwork($w, 'Cannot play track with uri '.$uri, './images/warning.png', 'Error!');
    }
}

/**
 * playUriWithMopidy function.
 *
 * @param mixed $w
 * @param mixed $uri
 */
function playUriWithMopidy($w, $uri)
{
    invokeMopidyMethod($w, 'core.tracklist.clear', array());
    playUriWithMopidyWithoutClearing($w, $uri);
}

/**
 * playTrackInContextWithMopidy function.
 *
 * @param mixed $w
 * @param mixed $track_uri
 * @param mixed $context_uri
 */
function playTrackInContextWithMopidy($w, $track_uri, $context_uri)
{
    invokeMopidyMethod($w, 'core.tracklist.clear', array());
    invokeMopidyMethod($w, 'core.tracklist.add', array('uri' => $context_uri, 'at_position' => 0));
    $tl_tracks = invokeMopidyMethod($w, 'core.tracklist.get_tl_tracks', array());

    // loop to find track_uri
    $i = 0;
    foreach ($tl_tracks as $tl_track) {
        if ($tl_track->track->uri == $track_uri) {
            // found the track move it to position 0
            invokeMopidyMethod($w, 'core.tracklist.move', array('start' => $i, 'end' => $i, 'to_position' => 0));
        }
        ++$i;
    }

    $tl_tracks = invokeMopidyMethod($w, 'core.tracklist.get_tl_tracks', array());
    invokeMopidyMethod($w, 'core.playback.play', array('tl_track' => $tl_tracks[0]));
}

/**
 * setThePlaylistPrivacy function.
 *
 * @param mixed $w
 * @param mixed $playlist_uri
 * @param mixed $playlist_name
 * @param bool  $public
 */
function setThePlaylistPrivacy($w, $playlist_uri, $playlist_name, $public)
{
    try {
        $tmp = explode(':', $playlist_uri);
        $api = getSpotifyWebAPI($w);
        $ret = $api->updateUserPlaylist(urlencode($tmp[2]), $tmp[4], array('name' => escapeQuery($playlist_name),
                'public' => $public, ));
        if ($ret == true) {
            // refresh library
            refreshLibrary($w);
        }
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(updateUserPlaylist): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }
}

/**
 * followThePlaylist function.
 *
 * @param mixed $w
 * @param mixed $playlist_uri
 */
function followThePlaylist($w, $playlist_uri)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $is_public_playlists = $settings->is_public_playlists;

    $public = false;
    if ($is_public_playlists) {
        $public = true;
    }
    try {
        $tmp = explode(':', $playlist_uri);
        $api = getSpotifyWebAPI($w);
        $ret = $api->followPlaylist(urlencode($tmp[2]), $tmp[4], array('public' => $public));
        if ($ret == true) {
            // refresh library
            refreshLibrary($w);
        }
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(followThePlaylist): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }
}

/**
 * unfollowThePlaylist function.
 *
 * @param mixed $w
 * @param mixed $playlist_uri
 */
function unfollowThePlaylist($w, $playlist_uri)
{
    try {
        $tmp = explode(':', $playlist_uri);
        $api = getSpotifyWebAPI($w);
        $ret = $api->unfollowPlaylist($tmp[2], $tmp[4]);
        if ($ret == true) {
            // refresh library
            refreshLibrary($w);
        }
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(unfollowPlaylist): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }
}

/**
 * addPlaylistToPlayQueue function.
 *
 * @param mixed $w
 * @param mixed $playlist_uri
 * @param mixed $playlist_name
 */
function addPlaylistToPlayQueue($w, $playlist_uri, $playlist_name)
{
    if (!$w->internet()) {
        return false;
    }

    // Read settings from JSON

    $settings = getSettings($w);
    $output_application = $settings->output_application;

    if ($output_application != 'MOPIDY') {
        $tracks = getThePlaylistFullTracks($w, $playlist_uri);
        if ($tracks == false) {
            displayNotificationWithArtwork($w, 'Cannot get tracks for playlist '.$playlist_name, './images/warning.png', 'Error!');

            return false;
        }
    } else {
        $tracks = array();
    }
    $playqueue = array(
        'type' => 'playlist',
        'uri' => $playlist_uri,
        'name' => escapeQuery($playlist_name),
        'current_track_index' => 0,
        'tracks' => $tracks,
    );
    $w->write($playqueue, 'playqueue.json');
}

/**
 * addAlbumToPlayQueue function.
 *
 * @param mixed $w
 * @param mixed $album_uri
 * @param mixed $album_name
 */
function addAlbumToPlayQueue($w, $album_uri, $album_name)
{
    if (!$w->internet()) {
        return false;
    }

    // Read settings from JSON

    $settings = getSettings($w);
    $output_application = $settings->output_application;

    if ($output_application != 'MOPIDY') {
        $tracks = getTheAlbumFullTracks($w, $album_uri);
        if ($tracks == false) {
            displayNotificationWithArtwork($w, 'Cannot get tracks for album '.$album_name, './images/warning.png', 'Error!');

            return false;
        }
    } else {
        $tracks = array();
    }

    $playqueue = array(
        'type' => 'album',
        'uri' => $album_uri,
        'name' => escapeQuery($album_name),
        'current_track_index' => 0,
        'tracks' => $tracks,
    );
    $w->write($playqueue, 'playqueue.json');
}

/**
 * addArtistToPlayQueue function.
 *
 * @param mixed $w
 * @param mixed $artist_uri
 * @param mixed $artist_name
 * @param mixed $country_code
 */
function addArtistToPlayQueue($w, $artist_uri, $artist_name, $country_code)
{
    if (!$w->internet()) {
        return false;
    }

    // Read settings from JSON

    $settings = getSettings($w);
    $output_application = $settings->output_application;
    $country_code = $settings->country_code;

    if ($output_application != 'MOPIDY') {
        $tracks = getTheArtistFullTracks($w, $artist_uri, $country_code);
        if ($tracks == false) {
            displayNotificationWithArtwork($w, 'Cannot get tracks for artist '.$artist_name, './images/warning.png', 'Error!');

            return false;
        }
    } else {
        $tracks = array();
    }

    $playqueue = array(
        'type' => 'artist',
        'uri' => $artist_uri,
        'name' => escapeQuery($artist_name),
        'current_track_index' => 0,
        'tracks' => $tracks,
    );
    $w->write($playqueue, 'playqueue.json');
}

/**
 * addTrackToPlayQueue function.
 *
 * @param mixed $w
 * @param mixed $track_uri
 * @param mixed $track_name
 * @param mixed $artist_name
 * @param mixed $album_name
 * @param mixed $duration
 * @param mixed $country_code
 */
function addTrackToPlayQueue($w, $track_uri, $track_name, $artist_name, $album_name, $duration, $country_code)
{
    if (!$w->internet()) {
        return false;
    }

    // Read settings from JSON

    $settings = getSettings($w);
    $output_application = $settings->output_application;

    $track = new stdClass();
    if ($output_application != 'MOPIDY') {
        $tracks = array();
        $track = getTheFullTrack($w, $track_uri, $country_code);
        if ($track == false) {
            $track = new stdClass();
            $track->uri = $track_uri;
            $track->name = $track_name;
            $artists = array();
            $artist = new stdClass();
            $artist->name = $artist_name;
            $artists[0] = $artist;
            $track->artists = $artists;
            $album = new stdClass();
            $album->name = $album_name;
            $track->album = $album;
            if (is_numeric($duration)) {
                $track->duration_ms = $duration * 1000;
            } else {
                $track->duration = $duration;
            }
        }
    } else {
        $tracks = array();
    }

    $playqueue = $w->read('playqueue.json');
    if ($playqueue == false) {
        $tracks[] = $track;
        $newplayqueue = array(
            'type' => 'track',
            'uri' => $track_uri,
            'name' => escapeQuery($track_name),
            'current_track_index' => 0,
            'tracks' => $tracks,
        );
    } else {
        // replace current track by new track
        $playqueue->tracks[$playqueue->current_track_index] = $track;
        if ($output_application != 'MOPIDY') {
            $tracks = $playqueue->tracks;
        }
        if ($playqueue->type != '') {
            $newplayqueue = array(
                'type' => $playqueue->type,
                'uri' => $playqueue->uri,
                'name' => $playqueue->name,
                'current_track_index' => $playqueue->current_track_index,
                'tracks' => $tracks,
            );
        } else {
            $newplayqueue = array(
                'type' => 'track',
                'uri' => $track_uri,
                'name' => escapeQuery($track_name),
                'current_track_index' => $playqueue->current_track_index,
                'tracks' => $tracks,
            );
        }
    }
    $w->write($newplayqueue, 'playqueue.json');
}

/**
 * updateCurrentTrackIndexFromPlayQueue function.
 *
 * @param mixed $w
 */
function updateCurrentTrackIndexFromPlayQueue($w)
{
    $playqueue = $w->read('playqueue.json');
    if ($playqueue == false) {
        displayNotificationWithArtwork($w, 'No play queue yet', './images/warning.png', 'Error!');
    }

    // Read settings from JSON

    $settings = getSettings($w);
    
    $output_application = $settings->output_application;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);
        $found = false;
        $i = 0;
        $current_track_name = cleanupTrackName($results[0]);
        if (isset($playqueue->tracks)) {
            if (count($playqueue->tracks) > 0) {
                foreach ($playqueue->tracks as $track) {
                    $track_name = cleanupTrackName($track->name);
                    if (escapeQuery($track_name) == escapeQuery($current_track_name) &&
                        escapeQuery($track->artists[0]->name) == escapeQuery($results[1])) {
                        $found = true;
                        break;
                    }
                    ++$i;
                }
            }
        }

        if ($found == false) {
            // empty queue
            $newplayqueue = array(
                'type' => '',
                'uri' => '',
                'name' => '',
                'current_track_index' => 0,
                'tracks' => array(),
            );
            // displayNotificationWithArtwork($w,"Play Queue has been reset!", './images/warning.png', 'Error!');
        } else {
            $newplayqueue = array(
                'type' => $playqueue->type,
                'uri' => $playqueue->uri,
                'name' => $playqueue->name,
                'current_track_index' => $i,
                'tracks' => $playqueue->tracks,
            );
        }
        $w->write($newplayqueue, 'playqueue.json');
    } else {
        displayNotificationWithArtwork($w, 'No track is playing', './images/warning.png');
    }
}

/**
 * getBiography function.
 *
 * @param mixed $w
 * @param mixed $artist_uri
 * @param mixed $artist_name
 */
function getBiography($w, $artist_uri, $artist_name)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $echonest_api_key = $settings->echonest_api_key;

    // THIS IS BROKEN, see http://developer.echonest.com
    // SPOTIFY WEB API DOES NO SUPPORT IT YET https://github.com/spotify/web-api/issues/207

    $json = doJsonRequest($w, 'http://developer.echonest.com/api/v4/artist/biographies?api_key='.$echonest_api_key.'&id='.$artist_uri);
    $response = $json->response;

    foreach ($response->biographies as $biography) {
        if ($biography->site == 'wikipedia') {
            $wikipedia = $biography->text;
            $wikipedia_url = $biography->url;
        }
        if ($biography->site == 'last.fm') {
            $lastfm = $biography->text;
            $lastfm_url = $biography->url;
        }
        $default = 'Source: '.$biography->site.'\n'.$biography->text;
        $default_url = $biography->url;
    }

    if ($lastfm) {
        $text = $lastfm;
        $source = 'Last FM';
        $url = $lastfm_url;
    } elseif ($wikipedia) {
        $text = $wikipedia;
        $source = 'Wikipedia';
        $url = $wikipedia_url;
    } else {
        $text = $default;
        $source = $biography->site;
        $url = $default_url;
    }
    if ($text == '') {
        return array(false, '', '', '');
    }
    $output = strip_tags($text);

    // Get URLs of artist, if available
    $json = doJsonRequest($w, 'http://developer.echonest.com/api/v4/artist/urls?api_key='.$echonest_api_key.'&id='.$artist_uri);

    $twitter_url = '';
    if (isset($json->response->urls->twitter_url)) {
        $twitter_url = $json->response->urls->twitter_url;
    }

    $official_url = '';
    if (isset($json->response->urls->official_url)) {
        $official_url = $json->response->urls->official_url;
    }

    return array($url, $source, $output, $twitter_url, $official_url);
}

/**
 * searchWebApi function.
 *
 * @param mixed $w
 * @param mixed $country_code
 * @param mixed $query
 * @param mixed $type
 * @param int   $limit        (default: 50)
 * @param bool  $actionMode   (default: true)
 */
function searchWebApi($w, $country_code, $query, $type, $limit = 50, $actionMode = true)
{
    $results = array();

    try {
        if ($limit != 50) {
            $limitSearch = $limit;
        } else {
            $limitSearch = 50;
        }
        $api = getSpotifyWebAPI($w);
        $searchResults = $api->search($query, $type, array(
                'market' => $country_code,
                'limit' => $limitSearch,
            ));

        if ($type == 'artist') {
            foreach ($searchResults->artists->items as $item) {
                $results[] = $item;
            }
        } elseif ($type == 'track') {
            foreach ($searchResults->tracks->items as $item) {
                $results[] = $item;
            }
        } elseif ($type == 'album') {
            foreach ($searchResults->albums->items as $item) {
                $results[] = $item;
            }
        } elseif ($type == 'playlist') {
            foreach ($searchResults->playlists->items as $item) {
                $results[] = $item;
            }
        }
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        if ($actionMode == true) {
            logMsg('Error(search): (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);
        } else {
            $w2 = new Workflows('com.vdesabou.spotify.mini.player');
            $w2->result(null, '', 'Error: Spotify WEB API returned error '.$e->getMessage(), 'Try again or report to author', './images/warning.png', 'no', null, '');
            echo $w2->toxml();
        }

        return false;
    }

    return $results;
}

/**
 * playAlfredPlaylist function.
 *
 * @param mixed $w
 */
function playAlfredPlaylist($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $is_alfred_playlist_active = $settings->is_alfred_playlist_active;
    $alfred_playlist_uri = $settings->alfred_playlist_uri;
    $alfred_playlist_name = $settings->alfred_playlist_name;
    $output_application = $settings->output_application;
    $use_artworks = $settings->use_artworks;

    if ($alfred_playlist_uri == '' || $alfred_playlist_name == '') {
        displayNotificationWithArtwork($w, 'Alfred Playlist is not set', './images/warning.png');

        return;
    }
    if ($output_application == 'MOPIDY') {
        playUriWithMopidy($w, $alfred_playlist_uri);
    } else if($output_application == 'APPLESCRIPT') {
        exec("osascript -e 'tell application \"Spotify\" to play track \"$alfred_playlist_uri\"'");
        addPlaylistToPlayQueue($w, $alfred_playlist_uri, $alfred_playlist_name);
    } else {
        $device_id = getSpotifyConnectCurrentDeviceId($w);
        if($device_id != '') {
            playTrackSpotifyConnect($w, $device_id, '', $alfred_playlist_uri);
        } else {
            displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
        }
    }

    $playlist_artwork_path = getPlaylistArtwork($w, $alfred_playlist_uri, true, true, $use_artworks);
    displayNotificationWithArtwork($w, '🔈 Alfred Playlist '.$alfred_playlist_name, $playlist_artwork_path, 'Play Alfred Playlist');
}

/**
 * lookupCurrentArtist function.
 *
 * @param mixed $w
 */
function lookupCurrentArtist($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);
        $tmp = explode(':', $results[4]);
        if (isset($tmp[1]) && $tmp[1] == 'local') {
            $artist_uri = getArtistUriFromSearch($w, $results[1]);
        } else {
            $artist_uri = getArtistUriFromTrack($w, $results[4]);
        }

        if ($artist_uri == false) {
            displayNotificationWithArtwork($w, 'Cannot get current artist', './images/warning.png', 'Error!');

            return;
        }
        exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Online▹'.$artist_uri.'@'.escapeQuery($results[1]).'▹'."\"'");
    } else {
        displayNotificationWithArtwork($w, 'No track is playing', './images/warning.png');
    }
}

/**
 * displayCurrentArtistBiography function.
 *
 * @param mixed $w
 */
function displayCurrentArtistBiography($w)
{
    if (!$w->internet()) {
        displayNotificationWithArtwork($w, 'No internet connection', './images/warning.png');

        return;
    }

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);
        $tmp = explode(':', $results[4]);
        if (isset($tmp[1]) && $tmp[1] == 'local') {
            $artist_uri = getArtistUriFromSearch($w, $results[1]);
        } else {
            $artist_uri = getArtistUriFromTrack($w, $results[4]);
        }
        if ($artist_uri == false) {
            displayNotificationWithArtwork($w, 'Cannot get current artist', './images/warning.png', 'Error!');

            return;
        }
        exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Biography▹'.$artist_uri.'∙'.escapeQuery($results[1]).'▹'."\"'");
    } else {
        displayNotificationWithArtwork($w, 'No artist is playing', './images/warning.png');
    }
}

/**
 * playCurrentArtist function.
 *
 * @param mixed $w
 */
function playCurrentArtist($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    $country_code = $settings->country_code;
    $use_artworks = $settings->use_artworks;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);
        $tmp = explode(':', $results[4]);
        if (isset($tmp[1]) && $tmp[1] == 'local') {
            $artist_uri = getArtistUriFromSearch($w, $results[1]);
        } else {
            $artist_uri = getArtistUriFromTrack($w, $results[4]);
        }
        if ($artist_uri == false) {
            displayNotificationWithArtwork($w, 'Cannot get current artist', './images/warning.png', 'Error!');

            return;
        }
        if ($output_application == 'MOPIDY') {
            playUriWithMopidy($w, $artist_uri);
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to play track \"$artist_uri\"'");
            addArtistToPlayQueue($w, $artist_uri, escapeQuery($results[1]), $country_code);
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                playTrackSpotifyConnect($w, $device_id, '', $artist_uri);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        displayNotificationWithArtwork($w, '🔈 Artist '.escapeQuery($results[1]), getArtistArtwork($w, $artist_uri, $results[1], true, false, false, $use_artworks), 'Play Current Artist');
    } else {
        displayNotificationWithArtwork($w, 'No artist is playing', './images/warning.png');
    }
}

/**
 * playCurrentAlbum function.
 *
 * @param mixed $w
 */
function playCurrentAlbum($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    $use_artworks = $settings->use_artworks;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);
        $tmp = explode(':', $results[4]);
        $album_uri = getAlbumUriFromTrack($w, $results[4]);
        if ($album_uri == false) {
            displayNotificationWithArtwork($w, 'Cannot get current album', './images/warning.png', 'Error!');

            return;
        }
        exec("osascript -e 'tell application \"Spotify\" to play track \"$album_uri\"'");
        displayNotificationWithArtwork($w, '🔈 Album '.escapeQuery($results[2]), getTrackOrAlbumArtwork($w, $results[4], true, false, false, $use_artworks), 'Play Current Album', $use_artworks);
    } else {
        displayNotificationWithArtwork($w, 'No track is playing', './images/warning.png');
    }
}

/**
 * addCurrentTrackTo function.
 *
 * @param mixed $w
 */
function addCurrentTrackTo($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);
        $tmp = explode(':', $results[4]);
        if (isset($tmp[1]) && $tmp[1] == 'local') {

            // Read settings from JSON

            $settings = getSettings($w);
            $country_code = $settings->country_code;
            // local track, look it up online

            $query = 'track:'.strtolower(escapeQuery($results[0])).' artist:'.strtolower(escapeQuery($results[1]));
            $searchResults = searchWebApi($w, $country_code, $query, 'track', 1);

            if (count($searchResults) > 0) {
                // only one track returned
                $track = $searchResults[0];
                $artists = $track->artists;
                $artist = $artists[0];
                $album = $track->album;
                logMsg("Unknown track $results[4] / $results[0] / $results[1] replaced by track: $track->uri / $track->name / $artist->name / $album->uri");
                $results[4] = $track->uri;
            } else {
                logMsg("Could not find track: $results[4] / $results[0] / $results[1] ");
                displayNotificationWithArtwork($w, 'Local track '.escapeQuery($results[0]).' has not online match', './images/warning.png', 'Error!');

                return;
            }
        }
        exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Add▹'.$results[4].'∙'.escapeQuery($results[0]).'▹'."\"'");
    } else {
        displayNotificationWithArtwork($w, 'No track is playing', './images/warning.png');
    }
}

/**
 * removeCurrentTrackFrom function.
 *
 * @param mixed $w
 */
function removeCurrentTrackFrom($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);
        exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Remove▹'.$results[4].'∙'.escapeQuery($results[0]).'▹'."\"'");
    } else {
        displayNotificationWithArtwork($w, 'No track is playing', './images/warning.png');
    }
}

/**
 * addCurrentTrackToAlfredPlaylistOrYourMusic function.
 *
 * @param mixed $w
 */
function addCurrentTrackToAlfredPlaylistOrYourMusic($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $is_alfred_playlist_active = $settings->is_alfred_playlist_active;

    if ($is_alfred_playlist_active == true) {
        addCurrentTrackToAlfredPlaylist($w);
    } else {
        addCurrentTrackToYourMusic($w);
    }
}

/**
 * addCurrentTrackToAlfredPlaylist function.
 *
 * @param mixed $w
 */
function addCurrentTrackToAlfredPlaylist($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);

        // Read settings from JSON

        $settings = getSettings($w);

        $is_alfred_playlist_active = $settings->is_alfred_playlist_active;
        $alfred_playlist_uri = $settings->alfred_playlist_uri;
        $alfred_playlist_name = $settings->alfred_playlist_name;
        $country_code = $settings->country_code;
        $use_artworks = $settings->use_artworks;

        if ($alfred_playlist_uri == '' || $alfred_playlist_name == '') {
            displayNotificationWithArtwork($w, 'Alfred Playlist is not set', './images/warning.png');

            return;
        }

        $tmp = explode(':', $results[4]);
        if (isset($tmp[1]) && $tmp[1] == 'local') {
            // local track, look it up online

            $query = 'track:'.strtolower(escapeQuery($results[0])).' artist:'.strtolower(escapeQuery($results[1]));
            $searchResults = searchWebApi($w, $country_code, $query, 'track', 1);

            if (count($searchResults) > 0) {
                // only one track returned
                $track = $searchResults[0];
                $artists = $track->artists;
                $artist = $artists[0];
                $album = $track->album;
                logMsg("Unknown track $results[4] / $results[0] / $results[1] replaced by track: $track->uri / $track->name / $artist->name / $album->uri");
                $results[4] = $track->uri;
            } else {
                logMsg("Could not find track: $results[4] / $results[0] / $results[1] ");
                displayNotificationWithArtwork($w, 'Local track '.escapeQuery($results[0]).' has not online match', './images/warning.png', 'Error!');

                return;
            }
        }

        $tmp = explode(':', $results[4]);
        $ret = addTracksToPlaylist($w, $tmp[2], $alfred_playlist_uri, $alfred_playlist_name, false);
        if (is_numeric($ret) && $ret > 0) {
            displayNotificationWithArtwork($w, ''.escapeQuery($results[0]).' by '.escapeQuery($results[1]).' added to Alfred Playlist '.$alfred_playlist_name, getTrackOrAlbumArtwork($w, $results[4], true, false, false, $use_artworks), 'Add Current Track to Alfred Playlist');
        } elseif (is_numeric($ret) && $ret == 0) {
            displayNotificationWithArtwork($w, ''.escapeQuery($results[0]).' by '.escapeQuery($results[1]).' is already in Alfred Playlist '.$alfred_playlist_name, './images/warning.png', 'Error!');
        }
    } else {
        displayNotificationWithArtwork($w, 'No track is playing', './images/warning.png', 'Error!');
    }
}

/**
 * addCurrentTrackToYourMusic function.
 *
 * @param mixed $w
 */
function addCurrentTrackToYourMusic($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    $use_artworks = $settings->use_artworks;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);
        $tmp = explode(':', $results[4]);
        if (isset($tmp[1]) && $tmp[1] == 'local') {

            // Read settings from JSON

            $settings = getSettings($w);
            $country_code = $settings->country_code;
            // local track, look it up online

            $query = 'track:'.strtolower(escapeQuery($results[0])).' artist:'.strtolower(escapeQuery($results[1]));
            $searchResults = searchWebApi($w, $country_code, $query, 'track', 1);

            if (count($searchResults) > 0) {
                // only one track returned
                $track = $searchResults[0];
                $artists = $track->artists;
                $artist = $artists[0];
                $album = $track->album;
                logMsg("Unknown track $results[4] / $results[0] / $results[1] replaced by track: $track->uri / $track->name / $artist->name / $album->uri");
                $results[4] = $track->uri;
            } else {
                logMsg("Could not find track: $results[4] / $results[0] / $results[1] ");
                displayNotificationWithArtwork($w, 'Local track '.escapeQuery($results[0]).' has not online match', './images/warning.png', 'Error!');

                return;
            }
        }
        $tmp = explode(':', $results[4]);
        $ret = addTracksToYourMusic($w, $tmp[2], false);
        if (is_numeric($ret) && $ret > 0) {
            displayNotificationWithArtwork($w, ''.escapeQuery($results[0]).' by '.escapeQuery($results[1]).' added to Your Music', getTrackOrAlbumArtwork($w, $results[4], true, false, false, $use_artworks), 'Add Current Track to Your Music');
        } elseif (is_numeric($ret) && $ret == 0) {
            displayNotificationWithArtwork($w, ''.escapeQuery($results[0]).' by '.escapeQuery($results[1]).' is already in Your Music', './images/warning.png');
        }
    } else {
        displayNotificationWithArtwork($w, 'No track is playing', './images/warning.png', 'Error!');
    }
}

/**
 * addTracksToYourMusic function.
 *
 * @param mixed $w
 * @param mixed $tracks
 * @param bool  $allow_duplicate (default: true)
 */
function addTracksToYourMusic($w, $tracks, $allow_duplicate = true)
{
    $tracks = (array) $tracks;
    $tracks_with_no_dup = array();
    $tracks_contain = array();
    if (!$allow_duplicate) {
        try {
            $api = getSpotifyWebAPI($w);
            // Note: max 50 Ids
            $offset = 0;
            do {
                $output = array_slice($tracks, $offset, 50);
                $offset += 50;

                if (count($output)) {
                    // refresh api
                    $api = getSpotifyWebAPI($w, $api);
                    $tracks_contain = $api->myTracksContains($output);
                    for ($i = 0; $i < count($output); ++$i) {
                        if (!$tracks_contain[$i]) {
                            $tracks_with_no_dup[] = $output[$i];
                        }
                    }
                }
            } while (count($output) > 0);

            $tracks = $tracks_with_no_dup;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(addTracksToYourMusic): (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);

            return false;
        }
    }

    if (count($tracks) != 0) {
        try {
            $api = getSpotifyWebAPI($w);
            $offset = 0;
            do {
                $output = array_slice($tracks, $offset, 50);
                $offset += 50;

                if (count($output)) {
                    // refresh api
                    $api = getSpotifyWebAPI($w, $api);
                    $api->addMyTracks($output);
                }
            } while (count($output) > 0);
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(addTracksToYourMusic): (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);

            return false;
        }

        // refresh library
        refreshLibrary($w);
    }

    return count($tracks);
}

/**
 * addTracksToPlaylist function.
 *
 * @param mixed $w
 * @param mixed $tracks
 * @param mixed $playlist_uri
 * @param mixed $playlist_name
 * @param bool  $allow_duplicate (default: true)
 * @param bool  $refreshLibrary  (default: true)
 */
function addTracksToPlaylist($w, $tracks, $playlist_uri, $playlist_name, $allow_duplicate = true, $refreshLibrary = true)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $userid = $settings->userid;

    $tracks_with_no_dup = array();
    if (!$allow_duplicate) {
        $playlist_tracks = getThePlaylistTracks($w, $playlist_uri);
        foreach ((array) $tracks as $track) {
            if (!checkIfDuplicate($playlist_tracks, $track)) {
                $tracks_with_no_dup[] = $track;
            }
        }
        $tracks = $tracks_with_no_dup;
    }

    if (count($tracks) != 0) {
        try {
            $api = getSpotifyWebAPI($w);
            $tmp = explode(':', $playlist_uri);

            // Note: max 100 Ids
            $offset = 0;
            $i = 0;
            do {
                $output = array_slice($tracks, $offset, 100);
                $offset += 100;

                if (count($output)) {
                    // refresh api
                    $api = getSpotifyWebAPI($w, $api);

                    if(getenv('append_to_playlist_when_adding_tracks') == 0) {
                        $api->addUserPlaylistTracks(urlencode($userid), $tmp[4], $output, array(
                                'position' => 0,
                            ));
                    } else {
                        $api->addUserPlaylistTracks(urlencode($userid), $tmp[4], $output);
                    }
                    ++$i;
                }
                /*
                if($i % 30 === 0) {
                sleep(60);
                echo "Info: Throttling in addTracksToPlaylist";
                }
                */
            } while (count($output) > 0);
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(addTracksToPlaylist): (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);

            return false;
        }

        if ($refreshLibrary) {
            refreshLibrary($w);
        }
    }

    return count($tracks);
}

/**
 * removeTrackFromPlaylist function.
 *
 * @param mixed $w
 * @param mixed $track_uri
 * @param mixed $playlist_uri
 * @param mixed $playlist_name
 * @param bool  $refreshLibrary (default: true)
 */
function removeTrackFromPlaylist($w, $track_uri, $playlist_uri, $playlist_name, $refreshLibrary = true)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $userid = $settings->userid;

    try {
        $api = getSpotifyWebAPI($w);
        $tmp = explode(':', $playlist_uri);
        $api->deleteUserPlaylistTracks(urlencode($userid), $tmp[4], array(
                array(
                    'id' => $track_uri,
                ),
            ));
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(removeTrackFromPlaylist): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }

    if ($refreshLibrary) {
        refreshLibrary($w);
    }

    return true;
}

/**
 * removeTrackFromYourMusic function.
 *
 * @param mixed $w
 * @param mixed $track_uri
 * @param bool  $refreshLibrary (default: true)
 */
function removeTrackFromYourMusic($w, $track_uri, $refreshLibrary = true)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $userid = $settings->userid;

    try {
        $api = getSpotifyWebAPI($w);
        $api->deleteMyTracks($track_uri);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(removeTrackFromYourMusic): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }

    if ($refreshLibrary) {
        refreshLibrary($w);
    }

    return true;
}

/**
 * getRandomTrack function.
 *
 * @param mixed $w
 */
function getRandomTrack($w)
{
    // check for library DB
    $dbfile = '';
    if (file_exists($w->data().'/update_library_in_progress')) {
        if (file_exists($w->data().'/library_old.db')) {
            $dbfile = $w->data().'/library_old.db';
        }
    } else {
        $dbfile = $w->data().'/library.db';
    }
    if ($dbfile == '') {
        return false;
    }

    // Get random track from DB

    try {
        $db = new PDO("sqlite:$dbfile", '', '', array(
                PDO::ATTR_PERSISTENT => true,
            ));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $getTracks = 'select uri,track_name,artist_name,album_name,duration from tracks order by random() limit 1';
        $stmt = $db->prepare($getTracks);
        $stmt->execute();
        $track = $stmt->fetch();
        $thetrackuri = $track[0];
        $thetrackname = $track[1];
        $theartistname = $track[2];
        $thealbumname = $track[3];
        $theduration = $track[4];
    } catch (PDOException $e) {
        handleDbIssuePdoEcho($db, $w);
    }

    return array($thetrackuri, $thetrackname, $theartistname, $thealbumname, $theduration);
}

/**
 * getRandomAlbum function.
 *
 * @param mixed $w
 */
function getRandomAlbum($w)
{
    // check for library DB
    $dbfile = '';
    if (file_exists($w->data().'/update_library_in_progress')) {
        if (file_exists($w->data().'/library_old.db')) {
            $dbfile = $w->data().'/library_old.db';
        }
    } else {
        $dbfile = $w->data().'/library.db';
    }
    if ($dbfile == '') {
        return false;
    }

    // Get random album from DB

    try {
        $db = new PDO("sqlite:$dbfile", '', '', array(
                PDO::ATTR_PERSISTENT => true,
            ));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $getTracks = 'select album_uri,album_name,artist_name from tracks order by random() limit 1';
        $stmt = $db->prepare($getTracks);
        $stmt->execute();
        $track = $stmt->fetch();
        $thealbumuri = $track[0];
        $thealbumname = $track[1];
        $theartistname = $track[2];
    } catch (PDOException $e) {
        handleDbIssuePdoEcho($db, $w);
    }

    return array($thealbumuri, $thealbumname, $theartistname);
}

/**
 * getArtistUriFromTrack function.
 *
 * @param mixed $w
 * @param mixed $track_uri
 */
function getArtistUriFromTrack($w, $track_uri)
{
    // Read settings from JSON

    $settings = getSettings($w);
    $country_code = $settings->country_code;

    try {
        $tmp = explode(':', $track_uri);

        if (isset($tmp[1]) && $tmp[1] == 'ad') {
            return false;
        }

        if (isset($tmp[1]) && $tmp[1] == 'local') {
            // local track, look it up online
            // spotify:local:The+D%c3%b8:On+My+Shoulders+-+Single:On+My+Shoulders:318
            // spotify:local:Damien+Rice:B-Sides:Woman+Like+a+Man+%28Live%2c+Unplugged%29:284

            $query = 'track:'.urldecode(strtolower($tmp[4])).' artist:'.urldecode(strtolower($tmp[2]));
            $results = searchWebApi($w, $country_code, $query, 'track', 1);

            if (count($results) > 0) {
                // only one track returned
                $track = $results[0];
                $artists = $track->artists;
                $artist = $artists[0];

                return $artist->uri;
            } else {
                logMsg("Could not find artist from uri: $track_uri");

                return false;
            }
        }
        $api = getSpotifyWebAPI($w);
        $track = $api->getTrack($tmp[2]);
        $artists = $track->artists;
        $artist = $artists[0];

        return $artist->uri;
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        //logMsg( 'Error(getArtistUriFromTrack): (exception '.print_r($e).')');
        //handleSpotifyWebAPIException($w, $e);
    }

    return false;
}

/**
 * getArtistUriFromSearch function.
 *
 * @param mixed $w
 * @param mixed $artist_name
 * @param mixed $country_code
 */
function getArtistUriFromSearch($w, $artist_name, $country_code = '')
{
    if ($artist_name == '') {
        return false;
    }
    if ($country_code == '') {

        // Read settings from JSON

        $settings = getSettings($w);

        $country_code = $settings->country_code;
    }
    $searchResults = searchWebApi($w, $country_code, $artist_name, 'artist', 1);

    if (count($searchResults) > 0) {
        // only one artist returned
        $artist = $searchResults[0];
    } else {
        return false;
    }

    return $artist->uri;
}

/**
 * getAlbumUriFromTrack function.
 *
 * @param mixed $w
 * @param mixed $track_uri
 */
function getAlbumUriFromTrack($w, $track_uri)
{
    try {
        $tmp = explode(':', $track_uri);

        if (isset($tmp[1]) && $tmp[1] == 'local') {

            // Read settings from JSON

            $settings = getSettings($w);
            $country_code = $settings->country_code;
            // local track, look it up online
            // spotify:local:The+D%c3%b8:On+My+Shoulders+-+Single:On+My+Shoulders:318
            // spotify:local:Damien+Rice:B-Sides:Woman+Like+a+Man+%28Live%2c+Unplugged%29:284

            $query = 'track:'.urldecode(strtolower($tmp[4])).' artist:'.urldecode(strtolower($tmp[2]));
            $results = searchWebApi($w, $country_code, $query, 'track', 1);

            if (count($results) > 0) {
                // only one track returned
                $track = $results[0];
                $album = $track->album;

                return $album->uri;
            } else {
                logMsg("Could not find album from uri: $track_uri");

                return false;
            }
        }
        $api = getSpotifyWebAPI($w);
        $track = $api->getTrack($tmp[2]);
        $album = $track->album;
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(getAlbumUriFromTrack): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }

    return $album->uri;
}

/**
 * clearPlaylist function.
 *
 * @param mixed $w
 * @param mixed $playlist_uri
 * @param mixed $playlist_name
 */
function clearPlaylist($w, $playlist_uri, $playlist_name)
{
    try {
        $tmp = explode(':', $playlist_uri);
        $emptytracks = array();
        $api = getSpotifyWebAPI($w);
        $api->replacePlaylistTracks(urlencode($tmp[2]), $tmp[4], $emptytracks);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(clearPlaylist): playlist uri '.$playlist_uri.' (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }

    // refresh library
    refreshLibrary($w);

    return true;
}

/**
 * createTheUserPlaylist function.
 *
 * @param mixed $w
 * @param mixed $playlist_name
 */
function createTheUserPlaylist($w, $playlist_name)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $userid = $settings->userid;
    $is_public_playlists = $settings->is_public_playlists;

    $public = false;
    if ($is_public_playlists) {
        $public = true;
    }
    try {
        $api = getSpotifyWebAPI($w);
        $json = $api->createUserPlaylist(urlencode($userid), array(
                'name' => $playlist_name,
                'public' => $public,
            ));
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(createUserPlaylist): createUserPlaylist '.$playlist_name.' (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }

    return $json->uri;
}

/**
 * createRadioArtistPlaylistForCurrentArtist function.
 *
 * @param mixed $w
 */
function createRadioArtistPlaylistForCurrentArtist($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);
        $tmp = explode(':', $results[4]);
        if (isset($tmp[1]) && $tmp[1] == 'local') {
            $artist_uri = getArtistUriFromSearch($w, $results[1]);
        } else {
            $artist_uri = getArtistUriFromTrack($w, $results[4]);
        }

        if ($artist_uri == false) {
            displayNotificationWithArtwork($w, 'Cannot get current artist', './images/warning.png', 'Error!');

            return;
        }
        createRadioArtistPlaylist($w, $results[1], $artist_uri);
    } else {
        displayNotificationWithArtwork($w, 'Cannot get current artist', './images/warning.png', 'Error!');
    }
}

/**
 * createRadioArtistPlaylist function.
 *
 * @param mixed $w
 * @param mixed $artist_name
 * @param mixed $artist_uri
 */
function createRadioArtistPlaylist($w, $artist_name, $artist_uri)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $radio_number_tracks = $settings->radio_number_tracks;
    $userid = $settings->userid;
    $is_public_playlists = $settings->is_public_playlists;
    $is_autoplay_playlist = $settings->is_autoplay_playlist;
    $country_code = $settings->country_code;
    $use_artworks = $settings->use_artworks;

    $public = false;
    if ($is_public_playlists) {
        $public = true;
    }

    try {
        $api = getSpotifyWebAPI($w);
        $tmp = explode(':', $artist_uri);
        $recommendations = $api->getRecommendations(array(
            'seed_artists' => array($tmp[2]),
            'market' => $country_code,
            'limit' => $radio_number_tracks,
        ));
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(createRadioArtistPlaylist): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);
        exit;
    }

    $newplaylisttracks = array();
    foreach ($recommendations->tracks as $track) {
        $newplaylisttracks[] = $track->id;
    }

    if (count($newplaylisttracks) > 0) {
        try {
            $api = getSpotifyWebAPI($w);
            $json = $api->createUserPlaylist($userid, array(
                    'name' => 'Artist radio for '.escapeQuery($artist_name),
                    'public' => $public,
                ));
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(createUserPlaylist): radio artist '.$artist_name.' (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);

            return false;
        }

        $ret = addTracksToPlaylist($w, $newplaylisttracks, $json->uri, $json->name, false, false);
        if (is_numeric($ret) && $ret > 0) {
            if ($is_autoplay_playlist) {
                sleep(2);
                exec("osascript -e 'tell application \"Spotify\" to play track \"$json->uri\"'");
                $playlist_artwork_path = getPlaylistArtwork($w, $json->uri, true, false, $use_artworks);
                displayNotificationWithArtwork($w, '🔈 Playlist '.$json->name, $playlist_artwork_path, 'Launch Artist Radio Playlist');
            }
            refreshLibrary($w);

            return;
        } elseif (is_numeric($ret) && $ret == 0) {
            displayNotificationWithArtwork($w, 'Playlist '.$json->name.' cannot be added', './images/warning.png', 'Error!');

            return;
        }
    } else {
        displayNotificationWithArtwork($w, 'Artist was not found in Echo Nest', './images/warning.png', 'Error!');

        return false;
    }

    return true;
}

/**
 * createCompleteCollectionArtistPlaylist function.
 *
 * @param mixed $w
 * @param mixed $artist_name
 */
function createCompleteCollectionArtistPlaylist($w, $artist_name, $artist_uri)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $userid = $settings->userid;
    $country_code = $settings->country_code;
    $is_public_playlists = $settings->is_public_playlists;
    $is_autoplay_playlist = $settings->is_autoplay_playlist;
    $use_artworks = $settings->use_artworks;

    $public = false;
    if ($is_public_playlists) {
        $public = true;
    }

    $newplaylisttracks = array();
    // call to web api, if it fails,
    // it displays an error in main window
    $albums = getTheArtistAlbums($w, $artist_uri, $country_code, true, false);

    foreach ($albums as $album) {
        // call to web api, if it fails,
        // it displays an error in main window
        $tracks = getTheAlbumFullTracks($w, $album->uri, true);
        foreach ($tracks as $track) {
            $tmp = explode(':', $track->uri);
            $newplaylisttracks[] = $tmp[2];
        }
    }

    if (count($newplaylisttracks) > 0) {
        try {
            $api = getSpotifyWebAPI($w);
            $json = $api->createUserPlaylist($userid, array(
                    'name' => 'CC for '.escapeQuery($artist_name),
                    'public' => $public,
                ));
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(createCompleteCollectionArtistPlaylist): Complete Collection '.$artist_name.' (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);

            return false;
        }

        $ret = addTracksToPlaylist($w, $newplaylisttracks, $json->uri, $json->name, false, false);
        if (is_numeric($ret) && $ret > 0) {
            if ($is_autoplay_playlist) {
                sleep(2);
                exec("osascript -e 'tell application \"Spotify\" to play track \"$json->uri\"'");
                $playlist_artwork_path = getPlaylistArtwork($w, $json->uri, true, false, $use_artworks);
                displayNotificationWithArtwork($w, '🔈 Playlist '.$json->name, $playlist_artwork_path, 'Launch Complete Collection Playlist');
            }
            refreshLibrary($w);

            return;
        } elseif (is_numeric($ret) && $ret == 0) {
            displayNotificationWithArtwork($w, 'Playlist '.$json->name.' cannot be added', './images/warning.png', 'Error!');

            return;
        }
    } else {
        displayNotificationWithArtwork($w, 'No track was found for artist '.$artist_name, './images/warning.png', 'Error!');

        return false;
    }

    return true;
}

/**
 * createRadioSongPlaylistForCurrentTrack function.
 *
 * @param mixed $w
 */
function createRadioSongPlaylistForCurrentTrack($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);
        createRadioSongPlaylist($w, $results[0], $results[4], $results[1]);
    } else {
        displayNotificationWithArtwork($w, 'There is not track currently playing', './images/warning.png', 'Error!');
    }
}

/**
 * createRadioSongPlaylist function.
 *
 * @param mixed $w
 * @param mixed $track_name
 * @param mixed $track_uri
 * @param mixed $artist_name
 */
function createRadioSongPlaylist($w, $track_name, $track_uri, $artist_name)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $radio_number_tracks = $settings->radio_number_tracks;
    $userid = $settings->userid;
    $country_code = $settings->country_code;
    $is_public_playlists = $settings->is_public_playlists;
    $is_autoplay_playlist = $settings->is_autoplay_playlist;
    $use_artworks = $settings->use_artworks;

    $public = false;
    if ($is_public_playlists) {
        $public = true;
    }

    $tmp = explode(':', $track_uri);
    if (isset($tmp[1]) && $tmp[1] == 'local') {
        // local track, look it up online
        // spotify:local:The+D%c3%b8:On+My+Shoulders+-+Single:On+My+Shoulders:318
        // spotify:local:Damien+Rice:B-Sides:Woman+Like+a+Man+%28Live%2c+Unplugged%29:284

        $query = 'track:'.urldecode(strtolower($tmp[4])).' artist:'.urldecode(strtolower($tmp[2]));
        $results = searchWebApi($w, $country_code, $query, 'track', 1);

        if (count($results) > 0) {
            // only one track returned
            $track = $results[0];
            $track_uri = $track->uri;
        } else {
            logMsg("Could not find track from uri: $track_uri");

            return false;
        }
    }

    $tmp = explode(':', $track_uri);
    try {
        $api = getSpotifyWebAPI($w);
        $recommendations = $api->getRecommendations(array(
            'seed_tracks' => array($tmp[2]),
            'market' => $country_code,
            'limit' => $radio_number_tracks,
        ));
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg( 'Error(createRadioSongPlaylist): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);
        exit;
    }

    $newplaylisttracks = array();
    foreach ($recommendations->tracks as $track) {
        $newplaylisttracks[] = $track->id;
    }

    if (count($newplaylisttracks) > 0) {
        try {
            $api = getSpotifyWebAPI($w);
            $json = $api->createUserPlaylist($userid, array(
                    'name' => 'Song radio for '.escapeQuery($track_name).' by '.escapeQuery($artist_name),
                    'public' => $public,
                ));
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(createUserPlaylist): radio song '.escapeQuery($track_name).' (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);

            return false;
        }

        $ret = addTracksToPlaylist($w, $newplaylisttracks, $json->uri, $json->name, false, false);
        if (is_numeric($ret) && $ret > 0) {
            if ($is_autoplay_playlist) {
                sleep(2);
                exec("osascript -e 'tell application \"Spotify\" to play track \"$json->uri\"'");
                $playlist_artwork_path = getPlaylistArtwork($w, $json->uri, true, false, $use_artworks);
                displayNotificationWithArtwork($w, '🔈 Playlist '.$json->name, $playlist_artwork_path, 'Launch Radio Playlist');
            }
            refreshLibrary($w);

            return;
        } elseif (is_numeric($ret) && $ret == 0) {
            displayNotificationWithArtwork($w, 'Playlist '.$json->name.' cannot be added', './images/warning.png', 'Error!');

            return;
        }
    } else {
        displayNotificationWithArtwork($w, 'Track was not found in Echo Nest', './images/warning.png', 'Error!');

        return false;
    }

    return true;
}

/**
 * getThePlaylistTracks function.
 *
 * @param mixed $w
 * @param mixed $playlist_uri
 */
function getThePlaylistTracks($w, $playlist_uri)
{
    $tracks = array();

    // Read settings from JSON

    $settings = getSettings($w);

    $country_code = $settings->country_code;
    try {
        $api = getSpotifyWebAPI($w);
        $tmp = explode(':', $playlist_uri);
        $offsetGetUserPlaylistTracks = 0;
        $limitGetUserPlaylistTracks = 100;
        do {
            // refresh api
            $api = getSpotifyWebAPI($w, $api);
            $userPlaylistTracks = $api->getUserPlaylistTracks($tmp[2], $tmp[4], array(
                    'fields' => array(
                        'total',
                        'items.track(id,is_playable,linked_from)',
                        'items(is_local)',
                    ),
                    'limit' => $limitGetUserPlaylistTracks,
                    'offset' => $offsetGetUserPlaylistTracks,
                    'market' => $country_code,
                ));

            foreach ($userPlaylistTracks->items as $item) {
                $track = $item->track;
                if (isset($track->is_playable) && $track->is_playable) {
                    if (isset($track->linked_from) && isset($track->linked_from->id)) {
                        $track->id = $track->linked_from->id;
                    }
                    $tracks[] = $track->id;
                }
                if (isset($item->is_local) && $item->is_local) {
                    $tracks[] = $track->id;
                }
            }

            $offsetGetUserPlaylistTracks += $limitGetUserPlaylistTracks;
        } while ($offsetGetUserPlaylistTracks < $userPlaylistTracks->total);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(getThePlaylistTracks): playlist uri '.$playlist_uri.' (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }

    return array_filter($tracks);
}

/**
 * getTheAlbumTracks function.
 *
 * @param mixed $w
 * @param mixed $album_uri
 */
function getTheAlbumTracks($w, $album_uri)
{
    $tracks = array();
    try {
        $api = getSpotifyWebAPI($w);
        $tmp = explode(':', $album_uri);
        $offsetGetAlbumTracks = 0;
        $limitGetAlbumTracks = 50;
        do {
            // refresh api
            $api = getSpotifyWebAPI($w, $api);
            $albumTracks = $api->getAlbumTracks($tmp[2], array(
                    'limit' => $limitGetAlbumTracks,
                    'offset' => $offsetGetAlbumTracks,
                ));

            foreach ($albumTracks->items as $track) {
                $tracks[] = $track->id;
            }
            $offsetGetAlbumTracks += $limitGetAlbumTracks;
        } while ($offsetGetAlbumTracks < $albumTracks->total);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(getTheAlbumTracks): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }

    return array_filter($tracks);
}

/**
 * getTheArtistAlbums function.
 *
 * @param mixed $w
 * @param mixed $artist_uri
 * @param mixed $country_code
 * @param bool  $actionMode   (default: false)
 * @param bool  $all_type     (default: true)
 */
function getTheArtistAlbums($w, $artist_uri, $country_code, $actionMode = false, $all_type = true)
{
    $album_ids = array();

    try {
        $api = getSpotifyWebAPI($w);
        $tmp = explode(':', $artist_uri);
        $offsetGetArtistAlbums = 0;
        $limitGetArtistAlbums = 50;

        if ($all_type) {
            $album_type = array(
                        'album',
                        'single',
                        'compilation',
                    );
        } else {
            $album_type = array(
                        'album',
                        'single',
                    );
        }
        do {
            // refresh api
            $api = getSpotifyWebAPI($w, $api);
            $userArtistAlbums = $api->getArtistAlbums($tmp[2], array(
                    'album_type' => $album_type,
                    'market' => $country_code,
                    'limit' => $limitGetArtistAlbums,
                    'offset' => $offsetGetArtistAlbums,
                ));

            foreach ($userArtistAlbums->items as $album) {
                $album_ids[] = $album->id;
            }

            $offsetGetArtistAlbums += $limitGetArtistAlbums;
        } while ($offsetGetArtistAlbums < $userArtistAlbums->total);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        if ($actionMode == false) {
            $w2 = new Workflows('com.vdesabou.spotify.mini.player');
            $w2->result(null, '', 'Error: Spotify WEB API getArtistAlbums returned error '.$e->getMessage(), 'Try again or report to author', './images/warning.png', 'no', null, '');
            echo $w2->toxml();
            exit;
        } else {
            logMsg( 'Error(getTheArtistAlbums): (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);

            return false;
        }
    }

    $albums = array();

    try {
        // Note: max 20 Ids
        $offset = 0;
        do {
            $output = array_slice($album_ids, $offset, 20);
            $offset += 20;

            if (count($output)) {
                // refresh api
                $api = getSpotifyWebAPI($w, $api);
                $resultGetAlbums = $api->getAlbums($output);
                foreach ($resultGetAlbums->albums as $album) {
                    $albums[] = $album;
                }
            }
        } while (count($output) > 0);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        $w2 = new Workflows('com.vdesabou.spotify.mini.player');
        $w2->result(null, '', 'Error: Spotify WEB API getAlbums returned error '.$e->getMessage(), 'Try again or report to author', './images/warning.png', 'no', null, '');
        echo $w2->toxml();
        exit;
    }

    return $albums;
}

/**
 * getTheAlbumFullTracks function.
 *
 * @param mixed $w
 * @param mixed $album_uri
 * @param bool  $actionMode (default: true)
 */
function getTheAlbumFullTracks($w, $album_uri, $actionMode = false)
{
    $tracks = array();

    // Read settings from JSON

    $settings = getSettings($w);
    $country_code = $settings->country_code;

    try {
        $api = getSpotifyWebAPI($w);
        $tmp = explode(':', $album_uri);
        $offsetGetAlbumTracks = 0;
        $limitGetAlbumTracks = 50;
        do {
            // refresh api
            $api = getSpotifyWebAPI($w, $api);
            $albumTracks = $api->getAlbumTracks($tmp[2], array(
                    'limit' => $limitGetAlbumTracks,
                    'offset' => $offsetGetAlbumTracks,
                    'market' => $country_code,
                ));

            foreach ($albumTracks->items as $track) {
                $tracks[] = $track;
            }

            $offsetGetAlbumTracks += $limitGetAlbumTracks;
        } while ($offsetGetAlbumTracks < $albumTracks->total);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        if ($actionMode == false) {
            $w2 = new Workflows('com.vdesabou.spotify.mini.player');
            $w2->result(null, '', 'Error: Spotify WEB API getAlbumTracks returned error '.$e->getMessage(), 'Try again or report to author', './images/warning.png', 'no', null, '');
            echo $w2->toxml();
            exit;
        } else {
            logMsg( 'Error(getTheAlbumFullTracks): (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);

            return false;
        }
    }

    return $tracks;
}

/**
 * getThePlaylistFullTracks function.
 *
 * @param mixed $w
 * @param mixed $playlist_uri
 */
function getThePlaylistFullTracks($w, $playlist_uri)
{
    $tracks = array();
    try {
        $api = getSpotifyWebAPI($w);
        $tmp = explode(':', $playlist_uri);
        $offsetGetUserPlaylistTracks = 0;
        $limitGetUserPlaylistTracks = 100;
        do {
            // refresh api
            $api = getSpotifyWebAPI($w, $api);
            $userPlaylistTracks = $api->getUserPlaylistTracks($tmp[2], $tmp[4], array(
                    'fields' => array(
                        'total',
                        'items(added_at)',
                        'items(is_local)',
                        'items.track(is_playable,duration_ms,uri,popularity,name,linked_from)',
                        'items.track.album(album_type,images,uri,name)',
                        'items.track.artists(name,uri)',
                    ),
                    'limit' => $limitGetUserPlaylistTracks,
                    'offset' => $offsetGetUserPlaylistTracks,
                ));

            foreach ($userPlaylistTracks->items as $item) {
                $tracks[] = $item->track;
            }

            $offsetGetUserPlaylistTracks += $limitGetUserPlaylistTracks;
        } while ($offsetGetUserPlaylistTracks < $userPlaylistTracks->total);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(getThePlaylistFullTracks): playlist uri '.$playlist_uri.' (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }

    return $tracks;
}

/**
 * getTheArtistFullTracks function.
 *
 * @param mixed $w
 * @param mixed $artist_uri
 * @param mixed $country_code
 */
function getTheArtistFullTracks($w, $artist_uri, $country_code)
{
    $tracks = array();
    try {
        $api = getSpotifyWebAPI($w);
        $tmp = explode(':', $artist_uri);
        $artistTopTracks = $api->getArtistTopTracks($tmp[2], array(
                    'country' => $country_code,
                ));

        foreach ($artistTopTracks->tracks as $track) {
            $tracks[] = $track;
        }
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(getTheArtistFullTracks): artist uri '.$artist_uri.' (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }

    return $tracks;
}

/**
 * getTheFullTrack function.
 *
 * @param mixed $w
 * @param mixed $track_uri
 * @param mixed $country_code
 */
function getTheFullTrack($w, $track_uri, $country_code)
{
    try {
        $tmp = explode(':', $track_uri);

        if (isset($tmp[1]) && $tmp[1] == 'local') {
            // local track, look it up online
            // spotify:local:The+D%c3%b8:On+My+Shoulders+-+Single:On+My+Shoulders:318
            // spotify:local:Damien+Rice:B-Sides:Woman+Like+a+Man+%28Live%2c+Unplugged%29:284

            $query = 'track:'.urldecode(strtolower($tmp[4])).' artist:'.urldecode(strtolower($tmp[2]));
            $results = searchWebApi($w, $country_code, $query, 'track', 1);

            if (count($results) > 0) {
                // only one track returned
                $track = $results[0];

                return $track;
            } else {
                logMsg("Could not find track from uri: $track_uri");

                return false;
            }
        }
        $api = getSpotifyWebAPI($w);
        $track = $api->getTrack($tmp[2]);

        return $track;
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg( 'Error(getTheFullTrack): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);
    }

    return false;
}

/**
 * getTheArtistRelatedArtists function.
 *
 * @param mixed $w
 * @param mixed $artist_uri
 */
function getTheArtistRelatedArtists($w, $artist_uri)
{
    $relateds = array();

    try {
        $api = getSpotifyWebAPI($w);
        $tmp = explode(':', $artist_uri);

        $relatedArtists = $api->getArtistRelatedArtists($tmp[2]);

        foreach ($relatedArtists->artists as $related) {
            $relateds[] = $related;
        }
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        $w2 = new Workflows('com.vdesabou.spotify.mini.player');
        $w2->result(null, '', 'Error: Spotify WEB API getArtistRelatedArtists returned error '.$e->getMessage(), 'Try again or report to author', './images/warning.png', 'no', null, '');
        echo $w2->toxml();
        exit;
    }

    return $relateds;
}

/**
 * getTheNewReleases function.
 *
 * @param mixed $w
 * @param mixed $country_code
 * @param mixed $max_results
 */
function getTheNewReleases($w, $country_code, $max_results = 50)
{
    $album_ids = array();

    try {
        $api = getSpotifyWebAPI($w);
        $offsetGetNewReleases = 0;
        $limitGetNewReleases = 50;
        do {
            // refresh api
            $api = getSpotifyWebAPI($w, $api);
            $newReleasesAlbums = $api->getNewReleases(array(
                    'country' => $country_code,
                    'limit' => $limitGetNewReleases,
                    'offset' => $offsetGetNewReleases,
                ));

            foreach ($newReleasesAlbums->albums->items as $album) {
                $album_ids[] = $album->id;
            }

            $offsetGetNewReleases += $limitGetNewReleases;
        } while ($offsetGetNewReleases < $max_results);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        $w2 = new Workflows('com.vdesabou.spotify.mini.player');
        $w2->result(null, '', 'Error: Spotify WEB API getNewReleases returned error '.$e->getMessage(), 'Try again or report to author', './images/warning.png', 'no', null, '');
        echo $w2->toxml();
        exit;
    }

    $albums = array();

    try {
        // Note: max 20 Ids
        $offset = 0;
        do {
            $output = array_slice($album_ids, $offset, 20);
            $offset += 20;

            if (count($output)) {
                // refresh api
                $api = getSpotifyWebAPI($w, $api);
                $resultGetAlbums = $api->getAlbums($output);
                foreach ($resultGetAlbums->albums as $album) {
                    $albums[] = $album;
                }
            }
        } while (count($output) > 0);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        $w2 = new Workflows('com.vdesabou.spotify.mini.player');
        $w2->result(null, '', 'Error: Spotify WEB API getNewReleases from getNewReleases returned error '.$e->getMessage(), 'Try again or report to author', './images/warning.png', 'no', null, '');
        echo $w2->toxml();
        exit;
    }

    return $albums;
}

/**
 * computeTime function.
 */
function computeTime()
{
    list($msec, $sec) = explode(' ', microtime());

    return (float) $sec + (float) $msec;
}

/**
 * truncateStr function.
 *
 * @param mixed $input
 * @param mixed $length
 */
function truncateStr($input, $length)
{
    // only truncate if input is actually longer than $length
    if (strlen($input) > $length) {
        // check if there are any spaces at all and if the last one is within
        // the given length if so truncate at space else truncate at length.
        if (strrchr($input, ' ') && strrchr($input, ' ') < $length) {
            return substr($input, 0, strrpos(substr($input, 0, $length), ' ')).'…';
        } else {
            return substr($input, 0, $length).'…';
        }
    } else {
        return $input;
    }
}

/**
 * getPlaylistsForTrack function.
 *
 * @param mixed $db
 * @param mixed $track_uri
 */
function getPlaylistsForTrack($db, $track_uri)
{
    $playlistsfortrack = '';
    $getPlaylistsForTrack = 'select distinct playlist_name from tracks where uri=:uri';
    try {
        $stmt = $db->prepare($getPlaylistsForTrack);
        $stmt->bindValue(':uri', ''.$track_uri.'');
        $stmt->execute();

        $noresult = true;
        while ($playlist = $stmt->fetch()) {
            if ($noresult == true) {
                if ($playlist[0] == '') {
                    $playlistsfortrack = $playlistsfortrack.' ● ♫ : '.'Your Music';
                } else {
                    $playlistsfortrack = $playlistsfortrack.' ● ♫ : '.truncateStr($playlist[0], 30);
                }
            } else {
                if ($playlist[0] == '') {
                    $playlistsfortrack = $playlistsfortrack.' ○ '.'Your Music';
                } else {
                    $playlistsfortrack = $playlistsfortrack.' ○ '.truncateStr($playlist[0], 30);
                }
            }
            $noresult = false;
        }
    } catch (PDOException $e) {
        return '';
    }

    return $playlistsfortrack;
}

/**
 * getNumberOfTracksForAlbum function.
 *
 * @param mixed $db
 * @param mixed $album_uri
 */
function getNumberOfTracksForAlbum($db, $album_uri, $yourmusiconly = false)
{
    if ($yourmusiconly == false) {
        $getNumberOfTracksForAlbum = 'select count(distinct track_name) from tracks where album_uri=:album_uri';
    } else {
        $getNumberOfTracksForAlbum = 'select count(distinct track_name) from tracks where yourmusic=1 and album_uri=:album_uri';
    }
    try {
        $stmt = $db->prepare($getNumberOfTracksForAlbum);
        $stmt->bindValue(':album_uri', ''.$album_uri.'');
        $stmt->execute();
        $nb = $stmt->fetch();
    } catch (PDOException $e) {
        return 0;
    }

    return $nb[0];
}

/**
 * getNumberOfTracksForArtist function.
 *
 * @param mixed $db
 * @param mixed $artist_name
 */
function getNumberOfTracksForArtist($db, $artist_name, $yourmusiconly = false)
{
    if ($yourmusiconly == false) {
        $getNumberOfTracksForArtist = 'select count(distinct track_name) from tracks where artist_name=:artist_name';
    } else {
        $getNumberOfTracksForArtist = 'select count(distinct track_name) from tracks where yourmusic=1 and artist_name=:artist_name';
    }

    try {
        $stmt = $db->prepare($getNumberOfTracksForArtist);
        $stmt->bindValue(':artist_name', ''.$artist_name.'');
        $stmt->execute();
        $nb = $stmt->fetch();
    } catch (PDOException $e) {
        return 0;
    }

    return $nb[0];
}

/**
 * escapeQuery function.
 *
 * @param mixed $text
 */
function escapeQuery($text)
{
    $text = str_replace("'", '’', $text);
    $text = str_replace('"', '', $text);
    $text = str_replace('&apos;', '’', $text);
    $text = str_replace('`', '’', $text);
    $text = str_replace('&amp;', 'and', $text);
    $text = str_replace('&', 'and', $text);
    $text = str_replace('\\', ' ', $text);
    $text = str_replace('$', '\$', $text);

    if (startswith($text, '’')) {
        $text = ltrim($text, '’');
    }

    return $text;
}

/**
 * checkIfResultAlreadyThere function.
 *
 * @param mixed $results
 * @param mixed $title
 */
function checkIfResultAlreadyThere($results, $title)
{
    foreach ($results as $result) {
        if ($result['title']) {
            if (strtolower($result['title']) == strtolower($title)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * checkIfDuplicate function.
 *
 * @param mixed $track_ids
 * @param mixed $id
 */
function checkIfDuplicate($track_ids, $id)
{
    foreach ($track_ids as $track_id) {
        if ($track_id == $id) {
            return true;
        }
    }

    return false;
}

/**
 * displayNotificationWithArtwork function.
 *
 * @param mixed  $w
 * @param mixed  $subtitle
 * @param mixed  $artwork
 * @param string $title    (default: 'Spotify Mini Player')
 */
function displayNotificationWithArtwork($w, $subtitle, $artwork, $title = 'Spotify Mini Player')
{

    // Read settings from JSON

    $settings = getSettings($w);
    $use_growl = $settings->use_growl;

    if (!$use_growl) {
        $theme_color = $settings->theme_color;
        if (!is_dir('./App/'.$theme_color.'/Spotify Mini Player.app') || (is_dir('./App/'.$theme_color.'/Spotify Mini Player.app') && filesize('./App/'.$theme_color.'/Spotify Mini Player.app') == 0)) {
            // reset to default
            updateSetting($w, 'theme_color', 'green');
            $theme_color = $settings->theme_color;
        }
        if ($artwork != '' && file_exists($artwork)) {
            copy($artwork, '/tmp/tmp');
        }
        exec("./terminal-notifier.app/Contents/MacOS/terminal-notifier -title '".$title."' -sender 'com.spotify.miniplayer.".$theme_color."' -contentImage '/tmp/tmp' -message '".$subtitle."'");
    } else {
        exec('./src/growl_notification.ksh -t "'.$title.'" -s "'.$subtitle.'" >> "'.$w->cache().'/action.log" 2>&1 & ');
    }
}

/**
 * displayNotificationForCurrentTrack function.
 *
 * @param mixed $w
 */
function displayNotificationForCurrentTrack($w)
{

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    $is_display_rating = $settings->is_display_rating;
    $use_artworks = $settings->use_artworks;
    $now_playing_notifications = $settings->now_playing_notifications;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);

        $tmp = explode(':', $results[4]);

        if (isset($tmp[1]) && $tmp[1] == 'ad') {
            return;
        }

        // download artwork for current track view
        $album_artwork_path = getTrackOrAlbumArtwork($w, $results[4], true, false, false, $use_artworks);
        $artist_uri = getArtistUriFromTrack($w, $results[4]);
        if ($artist_uri != false) {
            $artist_artwork_path = getArtistArtwork($w, $artist_uri, $results[1], true, false, false, $use_artworks);
        }
        if($now_playing_notifications) {
            displayNotificationWithArtwork($w, '🔈 '.escapeQuery($results[0]).' by '.escapeQuery($results[1]).' in album '.escapeQuery($results[2]), $album_artwork_path, 'Now Playing '.floatToStars(($results[6] / 100) ? $is_display_rating : 0).' ('.beautifyTime($results[5] / 1000).')');
        }
    }
}

/**
 * displayLyricsForCurrentTrack function.
 *
 * @param mixed $w
 */
function displayLyricsForCurrentTrack($w)
{
    if (!$w->internet()) {
        displayNotificationWithArtwork($w, 'No internet connection', './images/warning.png');

        return;
    }

    // Read settings from JSON

    $settings = getSettings($w);

    $output_application = $settings->output_application;
    $always_display_lyrics_in_browser = $settings->always_display_lyrics_in_browser;
    

    if ($output_application == 'MOPIDY') {
        $retArr = array(getCurrentTrackInfoWithMopidy($w));
    } else if($output_application == 'APPLESCRIPT') {
        // get info on current song
        exec('./src/track_info.ksh 2>&1', $retArr, $retVal);
        if ($retVal != 0) {
            displayNotificationWithArtwork($w, 'AppleScript Exception: '.htmlspecialchars($retArr[0]).' use spot_mini_debug command', './images/warning.png', 'Error!');
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' AppleScript Exception: '.htmlspecialchars($retArr[0])."\"'");

            return;
        }
    } else {
        $retArr = array(getCurrentTrackInfoWithSpotifyConnect($w));
    }

    if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
        $results = explode('▹', $retArr[count($retArr) - 1]);

        if($always_display_lyrics_in_browser == false) {
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Lyrics▹'.$results[4].'∙'.escapeQuery($results[1]).'∙'.escapeQuery($results[0])."\"'");
        } else {
            // display lyrics in default browser
            list($lyrics_url, $lyrics) = getLyrics($w, escapeQuery($results[1]), escapeQuery($results[0]));

            if ($lyrics_url != false) {
                exec('open '.$lyrics_url); 
            } else {
                displayNotificationWithArtwork($w, 'No lyrics found!', './images/warning.png', 'Error!');
            }
        }

    } else {
        displayNotificationWithArtwork($w, 'There is not track currently playing', './images/warning.png', 'Error!');
    }
}

/**
 * downloadArtworks function.
 *
 * @param mixed $w
 */
function downloadArtworks($w)
{

    // Read settings from JSON

    $settings = getSettings($w);
    $userid = $settings->userid;
    $use_artworks = $settings->use_artworks;

    if (!$use_artworks) {
        return;
    }
    if (!$w->internet()) {
        displayNotificationWithArtwork($w, 'Download Artworks,
	No internet connection', './images/warning.png');

        return;
    }

    touch($w->data().'/download_artworks_in_progress');
    $w->write('Download Artworks▹'. 0 .'▹'. 0 .'▹'.time(), 'download_artworks_in_progress');
    $in_progress_data = $w->read('download_artworks_in_progress');
    $words = explode('▹', $in_progress_data);

    putenv('LANG=fr_FR.UTF-8');

    ini_set('memory_limit', '512M');

    // Get list of artworks to download from DB

    $nb_artworks_total = 0;
    $nb_artworks = 0;

    $dbfile = $w->data().'/fetch_artworks.db';
    if (file_exists($dbfile)) {
        try {
            $dbartworks = new PDO("sqlite:$dbfile", '', '', array(
                    PDO::ATTR_PERSISTENT => true,
                ));
            $dbartworks->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $getCount = 'select count(artist_name) from artists where already_fetched=0';
            $stmt = $dbartworks->prepare($getCount);
            $stmt->execute();
            $count = $stmt->fetch();
            $nb_artworks_total += intval($count[0]);

            $getCount = 'select count(track_uri) from tracks where already_fetched=0';
            $stmt = $dbartworks->prepare($getCount);
            $stmt->execute();
            $count = $stmt->fetch();
            $nb_artworks_total += intval($count[0]);

            $getCount = 'select count(album_uri) from albums where already_fetched=0';
            $stmt = $dbartworks->prepare($getCount);
            $stmt->execute();
            $count = $stmt->fetch();
            $nb_artworks_total += intval($count[0]);

            if ($nb_artworks_total != 0) {
                if(getenv('reduce_notifications') == 0) {
                    displayNotificationWithArtwork($w, 'Start downloading '.$nb_artworks_total.' artworks', './images/artworks.png', 'Artworks');
                }

                // artists
                $getArtists = 'select artist_uri,artist_name from artists where already_fetched=0';
                $stmtGetArtists = $dbartworks->prepare($getArtists);

                $updateArtist = 'update artists set already_fetched=1 where artist_uri=:artist_uri';
                $stmtUpdateArtist = $dbartworks->prepare($updateArtist);

                // tracks
                $getTracks = 'select track_uri from tracks where already_fetched=0';
                $stmtGetTracks = $dbartworks->prepare($getTracks);

                $updateTrack = 'update tracks set already_fetched=1 where track_uri=:track_uri';
                $stmtUpdateTrack = $dbartworks->prepare($updateTrack);

                // albums
                $getAlbums = 'select album_uri from albums where already_fetched=0';
                $stmtGetAlbums = $dbartworks->prepare($getAlbums);

                $updateAlbum = 'update albums set already_fetched=1 where album_uri=:album_uri';
                $stmtUpdateAlbum = $dbartworks->prepare($updateAlbum);

                ////
                // Artists

                $artists = $stmtGetArtists->execute();

                while ($artist = $stmtGetArtists->fetch()) {
                    $ret = getArtistArtwork($w, $artist[0], $artist[1], true, false, true, $use_artworks);
                    if ($ret == false) {
                        logMsg("WARN: $artist[0] $artist[1] artwork not found, using default");
                    } elseif (!is_string($ret)) {
                        //logMsg("INFO: $artist[0] $artist[1] artwork was fetched ");
                    } elseif (is_string($ret)) {
                        //logMsg("INFO: $artist[0] $artist[1] artwork was already there $ret ");
                    }

                    $stmtUpdateArtist->bindValue(':artist_uri', $artist[0]);
                    $stmtUpdateArtist->execute();

                    ++$nb_artworks;
                    if ($nb_artworks % 10 === 0) {
                        $w->write('Download Artworks▹'.$nb_artworks.'▹'.$nb_artworks_total.'▹'.$words[3], 'download_artworks_in_progress');
                    }
                }

                ////
                // Tracks

                $tracks = $stmtGetTracks->execute();

                while ($track = $stmtGetTracks->fetch()) {
                    $ret = getTrackOrAlbumArtwork($w, $track[0], true, false, true, $use_artworks);
                    if ($ret == false) {
                        logMsg("WARN: $track[0] artwork not found, using default");
                    } elseif (!is_string($ret)) {
                        //logMsg("INFO: $track[0] artwork was fetched ");
                    } elseif (is_string($ret)) {
                        //logMsg("INFO: $artist[0] artwork was already there $ret ");
                    }

                    $stmtUpdateTrack->bindValue(':track_uri', $track[0]);
                    $stmtUpdateTrack->execute();

                    ++$nb_artworks;
                    if ($nb_artworks % 10 === 0) {
                        $w->write('Download Artworks▹'.$nb_artworks.'▹'.$nb_artworks_total.'▹'.$words[3], 'download_artworks_in_progress');
                    }
                }

                ////
                // Albums

                $albums = $stmtGetAlbums->execute();

                while ($album = $stmtGetAlbums->fetch()) {
                    $ret = getTrackOrAlbumArtwork($w, $album[0], true, false, true, $use_artworks);
                    if ($ret == false) {
                        logMsg("WARN: $album[0] artwork not found, using default ");
                    } elseif (!is_string($ret)) {
                        //logMsg("INFO: $album[0] artwork was fetched ");
                    } elseif (is_string($ret)) {
                        //logMsg("INFO: $artist[0] artwork was already there $ret ");
                    }

                    $stmtUpdateAlbum->bindValue(':album_uri', $album[0]);
                    $stmtUpdateAlbum->execute();

                    ++$nb_artworks;
                    if ($nb_artworks % 5 === 0) {
                        $w->write('Download Artworks▹'.$nb_artworks.'▹'.$nb_artworks_total.'▹'.$words[3], 'download_artworks_in_progress');
                    }
                }
            }
        } catch (PDOException $e) {
            handleDbIssuePdoEcho($dbartworks, $w);
            $dbartworks = null;

            return false;
        }
    }
    deleteTheFile($w->data().'/download_artworks_in_progress');
    logMsg('End of Download Artworks');
    if ($nb_artworks_total != 0) {
        if(getenv('reduce_notifications') == 0) {
            $elapsed_time = time() - $words[3];
            displayNotificationWithArtwork($w, 'All artworks have been downloaded ('.$nb_artworks_total.' artworks) - took '.beautifyTime($elapsed_time, true), './images/artworks.png', 'Artworks');
        }
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'artworks', $nb_artworks_total);
        }
    }

    return true;
}

/**
 * getTrackOrAlbumArtwork function.
 *
 * @param mixed $w
 * @param mixed $spotifyURL
 * @param mixed $fetchIfNotPresent
 * @param bool  $useArtworks       (default: true)
 */
function getTrackOrAlbumArtwork($w, $spotifyURL, $fetchIfNotPresent, $fetchLater = false, $isLaterFetch = false, $useArtworks = true, $forceFetch = false)
{
    $hrefs = explode(':', $spotifyURL);
    $isAlbum = false;
    if ($hrefs[1] == 'album') {
        $isAlbum = true;
    }

    if (!$useArtworks) {
        if ($isAlbum) {
            return './images/albums.png';
        } else {
            return './images/tracks.png';
        }
    }

    if (!file_exists($w->data().'/artwork')):
        exec("mkdir '".$w->data()."/artwork'");
    endif;

    $currentArtwork = $w->data().'/artwork/'.hash('md5', $hrefs[2].'.png').'/'."$hrefs[2].png";
    $artwork = '';

    if ($fetchLater == true) {
        if (!is_file($currentArtwork)) {
            return array(
                false,
                $currentArtwork,
            );
        } else {
            return array(
                true,
                $currentArtwork,
            );
        }
        // always return currentArtwork
        return $currentArtwork;
    }

    if (!is_file($currentArtwork) || (is_file($currentArtwork) && filesize($currentArtwork) == 0) || $hrefs[2] == 'fakeuri' || $forceFetch) {
        if ($fetchIfNotPresent == true || (is_file($currentArtwork) && filesize($currentArtwork) == 0) || $forceFetch) {
            if($forceFetch) {
                $artwork = getArtworkURL($w, $hrefs[1], $hrefs[2], true);
            } else {
                $artwork = getArtworkURL($w, $hrefs[1], $hrefs[2]);
            }
            

            // if return 0, it is a 404 error, no need to fetch
            if (!empty($artwork) || (is_numeric($artwork) && $artwork != 0)) {
                if (!file_exists($w->data().'/artwork/'.hash('md5', $hrefs[2].'.png'))):
                    exec("mkdir '".$w->data().'/artwork/'.hash('md5', $hrefs[2].'.png')."'");
                endif;
                $fp = fopen($currentArtwork, 'w+');
                $options = array(
                    CURLOPT_FILE => $fp,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_TIMEOUT => 5,
                );

                $w->request("$artwork", $options);

                if ($isLaterFetch == true) {
                    return true;
                } else {
                    stathat_ez_count('AlfredSpotifyMiniPlayer', 'artworks', 1);
                }
            } else {
                if ($isLaterFetch == true) {
                    if (!file_exists($w->data().'/artwork/'.hash('md5', $hrefs[2].'.png'))):
                        exec("mkdir '".$w->data().'/artwork/'.hash('md5', $hrefs[2].'.png')."'");
                    endif;

                    if ($isAlbum) {
                        copy('./images/albums.png', $currentArtwork);
                    } else {
                        copy('./images/tracks.png', $currentArtwork);
                    }

                    return false;
                } else {
                    if ($isAlbum) {
                        return './images/albums.png';
                    } else {
                        return './images/tracks.png';
                    }
                }
            }
        } else {
            if ($isLaterFetch == true) {
                if (!file_exists($w->data().'/artwork/'.hash('md5', $hrefs[2].'.png'))):
                    exec("mkdir '".$w->data().'/artwork/'.hash('md5', $hrefs[2].'.png')."'");
                endif;

                if ($isAlbum) {
                    copy('./images/albums.png', $currentArtwork);
                } else {
                    copy('./images/tracks.png', $currentArtwork);
                }

                return false;
            } else {
                if ($isAlbum) {
                    return './images/albums.png';
                } else {
                    return './images/tracks.png';
                }
            }
        }
    } else {
        if (filesize($currentArtwork) == 0) {
            if ($isLaterFetch == true) {
                if (!file_exists($w->data().'/artwork/'.hash('md5', $hrefs[2].'.png'))):
                    exec("mkdir '".$w->data().'/artwork/'.hash('md5', $hrefs[2].'.png')."'");
                endif;

                if ($isAlbum) {
                    copy('./images/albums.png', $currentArtwork);
                } else {
                    copy('./images/tracks.png', $currentArtwork);
                }

                return false;
            } else {
                if ($isAlbum) {
                    return './images/albums.png';
                } else {
                    return './images/tracks.png';
                }
            }
        }
    }

    if (is_numeric($artwork) && $artwork == 0) {
        if ($isLaterFetch == true) {
            if (!file_exists($w->data().'/artwork/'.hash('md5', $hrefs[2].'.png'))):
                exec("mkdir '".$w->data().'/artwork/'.hash('md5', $hrefs[2].'.png')."'");
            endif;

            if ($isAlbum) {
                copy('./images/albums.png', $currentArtwork);
            } else {
                copy('./images/tracks.png', $currentArtwork);
            }

            return false;
        } else {
            if ($isAlbum) {
                return './images/albums.png';
            } else {
                return './images/tracks.png';
            }
        }
    } else {
        return $currentArtwork;
    }
}

/**
 * getPlaylistArtwork function.
 *
 * @param mixed $w
 * @param mixed $playlist_uri
 * @param mixed $fetchIfNotPresent
 * @param bool  $forceFetch        (default: false)
 * @param bool  $useArtworks       (default: true)
 */
function getPlaylistArtwork($w, $playlist_uri, $fetchIfNotPresent, $forceFetch = false, $useArtworks = true)
{
    if (!$useArtworks) {
        return './images/playlists.png';
    }

    $tmp = explode(':', $playlist_uri);
    $filename = ''.$tmp[2].'_'.$tmp[4];
    $artwork = '';

    if (!file_exists($w->data().'/artwork')):
        exec("mkdir '".$w->data()."/artwork'");
    endif;

    $currentArtwork = $w->data().'/artwork/'.hash('md5', $filename.'.png').'/'."$filename.png";

    if (!is_file($currentArtwork) || (is_file($currentArtwork) && filesize($currentArtwork) == 0) || $forceFetch) {
        if ($fetchIfNotPresent == true || (is_file($currentArtwork) && filesize($currentArtwork) == 0) || $forceFetch) {
            $artwork = getPlaylistArtworkURL($w, $playlist_uri);
            // if return 0, it is a 404 error, no need to fetch
            if (!empty($artwork) || (is_numeric($artwork) && $artwork != 0)) {
                if (!file_exists($w->data().'/artwork/'.hash('md5', $filename.'.png'))):
                    exec("mkdir '".$w->data().'/artwork/'.hash('md5', $filename.'.png')."'");
                endif;
                $fp = fopen($currentArtwork, 'w+');
                $options = array(
                    CURLOPT_FILE => $fp,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_TIMEOUT => 5,
                );

                $w->request("$artwork", $options);
                stathat_ez_count('AlfredSpotifyMiniPlayer', 'artworks', 1);
            } else {
                return './images/playlists.png';
            }
        } else {
            return './images/playlists.png';
        }
    } else {
        if (filesize($currentArtwork) == 0) {
            return './images/playlists.png';
        }
    }

    if (is_numeric($artwork) && $artwork == 0) {
        return './images/playlists.png';
    } else {
        return $currentArtwork;
    }
}

/**
 * getCategoryArtwork function.
 *
 * @param mixed $w
 * @param mixed $categoryId
 * @param mixed $categoryURI
 * @param mixed $fetchIfNotPresent
 * @param bool  $forceFetch        (default: false)
 * @param bool  $useArtworks       (default: true)
 */
function getCategoryArtwork($w, $categoryId, $categoryURI, $fetchIfNotPresent, $forceFetch = false, $useArtworks = true)
{
    if (!$useArtworks) {
        return './images/browse.png';
    }

    if (!file_exists($w->data().'/artwork')):
        exec("mkdir '".$w->data()."/artwork'");
    endif;

    $currentArtwork = $w->data().'/artwork/'.hash('md5', $categoryId.'.jpg').'/'."$categoryId.jpg";

    if (!is_file($currentArtwork) || (is_file($currentArtwork) && filesize($currentArtwork) == 0) || $forceFetch) {
        if ($fetchIfNotPresent == true || (is_file($currentArtwork) && filesize($currentArtwork) == 0) || $forceFetch) {
            if (!file_exists($w->data().'/artwork/'.hash('md5', $categoryId.'.jpg'))):
                exec("mkdir '".$w->data().'/artwork/'.hash('md5', $categoryId.'.jpg')."'");
            endif;
            $fp = fopen($currentArtwork, 'w+');
            $options = array(
                CURLOPT_FILE => $fp,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_TIMEOUT => 5,
            );
            $w->request("$categoryURI", $options);
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'artworks', 1);
        } else {
            return './images/browse.png';
        }
    } else {
        if (filesize($currentArtwork) == 0) {
            return './images/browse.png';
        }
    }

    return $currentArtwork;
}

/**
 * getArtistArtwork function.
 *
 * @param mixed $w
 * @param mixed $artist_uri
 * @param mixed $artist_name
 * @param bool  $fetchIfNotPresent (default: false)
 * @param bool  $fetchLater        (default: false)
 * @param bool  $isLaterFetch      (default: false)
 * @param bool  $useArtworks       (default: true)
 */
function getArtistArtwork($w, $artist_uri, $artist_name, $fetchIfNotPresent = false, $fetchLater = false, $isLaterFetch = false, $useArtworks = true)
{
    if (!$useArtworks) {
        return './images/artists.png';
    }
    $parsedArtist = urlencode(escapeQuery($artist_name));

    if (!file_exists($w->data().'/artwork')):
        exec("mkdir '".$w->data()."/artwork'");
    endif;

    $currentArtwork = $w->data().'/artwork/'.hash('md5', $parsedArtist.'.png').'/'."$parsedArtist.png";
    if ($artist_uri == '') {
        return './images/artists.png';
    }

    $tmp = explode(':', $artist_uri);
    if (isset($tmp[2])) {
        $artist_uri = $tmp[2];
    }
    $artwork = '';

    if ($fetchLater == true) {
        if (!is_file($currentArtwork)) {
            return array(
                false,
                $currentArtwork,
            );
        } else {
            return array(
                true,
                $currentArtwork,
            );
        }
        // always return currentArtwork
        return $currentArtwork;
    }

    if (!is_file($currentArtwork) || (is_file($currentArtwork) && filesize($currentArtwork) == 0)) {
        if ($fetchIfNotPresent == true || (is_file($currentArtwork) && filesize($currentArtwork) == 0)) {
            $artwork = getArtistArtworkURL($w, $artist_uri);

            // if return 0, it is a 404 error, no need to fetch
            if (!empty($artwork) || (is_numeric($artwork) && $artwork != 0)) {
                if (!file_exists($w->data().'/artwork/'.hash('md5', $parsedArtist.'.png'))):
                    exec("mkdir '".$w->data().'/artwork/'.hash('md5', $parsedArtist.'.png')."'");
                endif;
                $fp = fopen($currentArtwork, 'w+');
                $options = array(
                    CURLOPT_FILE => $fp,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_TIMEOUT => 5,
                );
                $w->request("$artwork", $options);
                stathat_ez_count('AlfredSpotifyMiniPlayer', 'artworks', 1);
                if ($isLaterFetch == true) {
                    return true;
                }
            } else {
                if ($isLaterFetch == true) {
                    if (!file_exists($w->data().'/artwork/'.hash('md5', $parsedArtist.'.png'))):
                        exec("mkdir '".$w->data().'/artwork/'.hash('md5', $parsedArtist.'.png')."'");
                    endif;
                    copy('./images/artists.png', $currentArtwork);

                    return false;
                } else {
                    return './images/artists.png';
                }
            }
        } else {
            if ($isLaterFetch == true) {
                if (!file_exists($w->data().'/artwork/'.hash('md5', $parsedArtist.'.png'))):
                    exec("mkdir '".$w->data().'/artwork/'.hash('md5', $parsedArtist.'.png')."'");
                endif;
                copy('./images/artists.png', $currentArtwork);

                return false;
            } else {
                return './images/artists.png';
            }
        }
    } else {
        if (filesize($currentArtwork) == 0) {
            if ($isLaterFetch == true) {
                if (!file_exists($w->data().'/artwork/'.hash('md5', $parsedArtist.'.png'))):
                    exec("mkdir '".$w->data().'/artwork/'.hash('md5', $parsedArtist.'.png')."'");
                endif;
                copy('./images/artists.png', $currentArtwork);

                return false;
            } else {
                return './images/artists.png';
            }
        }
    }

    if (is_numeric($artwork) && $artwork == 0) {
        if ($isLaterFetch == true) {
            if (!file_exists($w->data().'/artwork/'.hash('md5', $parsedArtist.'.png'))):
                exec("mkdir '".$w->data().'/artwork/'.hash('md5', $parsedArtist.'.png')."'");
            endif;
            copy('./images/artists.png', $currentArtwork);

            return false;
        } else {
            return './images/artists.png';
        }
    } else {
        return $currentArtwork;
    }
}

/**
 * getArtworkURL function.
 *
 * @param mixed $w
 * @param mixed $type
 * @param mixed $id
 * @param boolean $highRes
 */
function getArtworkURL($w, $type, $id, $highRes = false)
{
    $url = '';

    if (startswith($id, 'fake')) {
        return $url;
    }
    if ($type == 'track') {
        try {
            $api = getSpotifyWebAPI($w);
            $track = $api->getTrack($id);
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(getArtworkURL track): (exception '.print_r($e).')');

            return $url;
        }
        if (isset($track->album) && isset($track->album->images)) {

            if(!$highRes) {
                // 60 px
                if (isset($track->album->images[2]) && isset($track->album->images[2]->url)) {
                    return $track->album->images[2]->url;
                }

                // 300 px
                if (isset($track->album->images[1]) && isset($track->album->images[1]->url)) {
                    return $track->album->images[1]->url;
                }

                // 600 px
                if (isset($track->album->images[0]) && isset($track->album->images[0]->url)) {
                    return $track->album->images[0]->url;
                }
            } else {
                // 600 px
                if (isset($track->album->images[0]) && isset($track->album->images[0]->url)) {
                    return $track->album->images[0]->url;
                }  

                // 300 px
                if (isset($track->album->images[1]) && isset($track->album->images[1]->url)) {
                    return $track->album->images[1]->url;
                }            
            }


        }
    } else {
        try {
            $api = getSpotifyWebAPI($w);
            $album = $api->getAlbum($id);
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(getArtworkURL album): (exception '.print_r($e).')');
            return $url;
        }
        if (isset($album->images)) {

            if(!$highRes) {
                // 60 px
                if (isset($album->images[2]) && isset($album->images[2]->url)) {
                    return $album->images[2]->url;
                }

                // 300 px
                if (isset($album->images[1]) && isset($album->images[1]->url)) {
                    return $album->images[1]->url;
                }

                // 600 px
                if (isset($album->images[0]) && isset($album->images[0]->url)) {
                    return $album->images[0]->url;
                }
            } else {
                // 600 px
                if (isset($album->images[0]) && isset($album->images[0]->url)) {
                    return $album->images[0]->url;
                }

                // 300 px
                if (isset($album->images[1]) && isset($album->images[1]->url)) {
                    return $album->images[1]->url;
                }     
            }


        }
    }

    return $url;
}

/**
 * getPlaylistArtworkURL function.
 *
 * @param mixed $w
 * @param mixed $playlist_uri
 */
function getPlaylistArtworkURL($w, $playlist_uri)
{
    $url = '';
    $tmp = explode(':', $playlist_uri);
    try {
        $api = getSpotifyWebAPI($w);
        $playlist = $api->getUserPlaylist(urlencode($tmp[2]), $tmp[4], array(
                'fields' => array(
                    'images',
                ),
            ));
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg( 'Error(getPlaylistArtworkURL): (exception '.print_r($e).')');

        return $url;
    }
    if (isset($playlist->images)) {

        // 60 px
        if (isset($playlist->images[2]) && isset($playlist->images[2]->url)) {
            return $playlist->images[2]->url;
        }

        // 300 px
        if (isset($playlist->images[1]) && isset($playlist->images[1]->url)) {
            return $playlist->images[1]->url;
        }

        // 600 px
        if (isset($playlist->images[0]) && isset($playlist->images[0]->url)) {
            return $playlist->images[0]->url;
        }
    }

    return $url;
}

/**
 * getArtistArtworkURL function.
 *
 * @param mixed $w
 * @param mixed $artist_id
 */
function getArtistArtworkURL($w, $artist_id)
{
    $url = '';
    if (startswith($artist_id, 'fake')) {
        return $url;
    }
    try {
        $api = getSpotifyWebAPI($w);
        $artist = $api->getArtist($artist_id);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg( 'Error(getArtistArtworkURL): (exception '.print_r($e).')');

        return $url;
    }

    if (isset($artist->images)) {

        // 60 px
        if (isset($artist->images[2]) && isset($artist->images[2]->url)) {
            return $artist->images[2]->url;
        }

        // 300 px
        if (isset($artist->images[1]) && isset($artist->images[1]->url)) {
            return $artist->images[1]->url;
        }

        // 600 px
        if (isset($artist->images[0]) && isset($artist->images[0]->url)) {
            return $artist->images[0]->url;
        }
    }

    return $url;
}

/**
 * updateLibrary function.
 *
 * @param mixed $w
 */
function updateLibrary($w)
{
    touch($w->data().'/update_library_in_progress');
    $w->write('InitLibrary▹'. 0 .'▹'. 0 .'▹'.time().'▹'.'starting', 'update_library_in_progress');
    $in_progress_data = $w->read('update_library_in_progress');

    // Read settings from JSON

    $settings = getSettings($w);
    $country_code = $settings->country_code;
    $userid = $settings->userid;
    $use_artworks = $settings->use_artworks;

    $words = explode('▹', $in_progress_data);

    // move legacy artwork files in hash directories if needed

    if (file_exists($w->data().'/artwork')) {
        $folder = $w->data().'/artwork';
        if ($handle = opendir($folder)) {
            while (false !== ($file = readdir($handle))) {
                if (stristr($file, '.png')) {
                    exec("mkdir '".$w->data().'/artwork/'.hash('md5', $file)."'");
                    rename($folder.'/'.$file, $folder.'/'.hash('md5', $file).'/'.$file);
                }
            }
            closedir($handle);
        }
    }

    putenv('LANG=fr_FR.UTF-8');
    ini_set('memory_limit', '512M');
    if (file_exists($w->data().'/library.db')) {
        rename($w->data().'/library.db', $w->data().'/library_old.db');
    }
    deleteTheFile($w->data().'/library_new.db');
    $dbfile = $w->data().'/library_new.db';
    touch($dbfile);

    try {
        $db = new PDO("sqlite:$dbfile", '', '', array(
                PDO::ATTR_PERSISTENT => true,
            ));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->query('PRAGMA synchronous = OFF');
        $db->query('PRAGMA journal_mode = OFF');
        $db->query('PRAGMA temp_store = MEMORY');
        $db->query('PRAGMA count_changes = OFF');
        $db->query('PRAGMA PAGE_SIZE = 4096');
        $db->query('PRAGMA default_cache_size=700000');
        $db->query('PRAGMA cache_size=700000');
        $db->query('PRAGMA compile_options');
    } catch (PDOException $e) {
        logMsg( 'Error(updateLibrary): (exception '.print_r($e).')');
        handleDbIssuePdoEcho($db, $w);
        $db = null;

        return false;
    }

    if ($use_artworks) {
        // db for fetch artworks
        // kill previous process if running
        $pid = exec("ps -efx | grep \"php\" | egrep \"DOWNLOAD_ARTWORKS\" | grep -v grep | awk '{print $2}'");
        if ($pid != '') {
            logMsg("KILL Download daemon <$pid>");
            $ret = exec("kill -9 \"$pid\"");
        }
        $dbfile = $w->data().'/fetch_artworks.db';
        if (file_exists($dbfile)) {
            deleteTheFile($dbfile);
            touch($dbfile);
        }
        if (file_exists($w->data().'/download_artworks_in_progress')) {
            deleteTheFile($w->data().'/download_artworks_in_progress');
        }
        try {
            $dbartworks = new PDO("sqlite:$dbfile", '', '', array(
                    PDO::ATTR_PERSISTENT => true,
                ));
            $dbartworks->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            logMsg('Error(updateLibrary): (exception '.print_r($e).')');
            handleDbIssuePdoEcho($dbartworks, $w);
            $dbartworks = null;
            $db = null;

            return false;
        }

        // DB artowrks
        try {
            $dbartworks->exec('create table artists (artist_uri text PRIMARY KEY NOT NULL, artist_name text, already_fetched boolean)');
            $dbartworks->exec('create table tracks (track_uri text PRIMARY KEY NOT NULL, already_fetched boolean)');
            $dbartworks->exec('create table albums (album_uri text PRIMARY KEY NOT NULL, already_fetched boolean)');
        } catch (PDOException $e) {
            logMsg('Error(updateLibrary): (exception '.print_r($e).')');
            handleDbIssuePdoEcho($dbartworks, $w);
            $dbartworks = null;
            $db = null;

            return false;
        }
    }

    // get the total number of tracks
    $nb_tracktotal = 0;
    $nb_skipped = 0;
    $savedListPlaylist = array();
    try {
        $api = getSpotifyWebAPI($w);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        logMsg('Error(getUserPlaylists): (exception '.print_r($e).')');
        handleSpotifyWebAPIException($w, $e);

        return false;
    }

    $offsetGetUserPlaylists = 0;
    $limitGetUserPlaylists = 50;
    do {
        $retry = true;
        $nb_retry = 0;
        while ($retry) {
            try {
                // refresh api
                $api = getSpotifyWebAPI($w, $api);
                $userPlaylists = $api->getUserPlaylists(urlencode($userid), array(
                        'limit' => $limitGetUserPlaylists,
                        'offset' => $offsetGetUserPlaylists,
                    ));
                $retry = false;
            } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                logMsg('Error(getUserPlaylists): retry '.$nb_retry.' (exception '.print_r($e).')');
                if ($e->getCode() == 429) { // 429 is Too Many Requests
                    $lastResponse = $api->getRequest()->getLastResponse();
                    $retryAfter = $lastResponse['headers']['Retry-After'];
                    sleep(retryAfter);
                } else if ($e->getCode() == 404) {
                        // skip
                        break;
                } else if ($e->getCode() == 500
                    || $e->getCode() == 502 || $e->getCode() == 503) {
                    // retry
                    if ($nb_retry > 20) {
                        handleSpotifyWebAPIException($w, $e);
                        $retry = false;

                        return false;
                    }
                    ++$nb_retry;
                    sleep(15);
                } else {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
            }
        }

        foreach ($userPlaylists->items as $playlist) {
            $tracks = $playlist->tracks;
            $nb_tracktotal += $tracks->total;
            if ($playlist->name != '') {
                $savedListPlaylist[] = $playlist;
            }
        }
        $offsetGetUserPlaylists += $limitGetUserPlaylists;
    } while ($offsetGetUserPlaylists < $userPlaylists->total);

    $savedMySavedTracks = array();
    $offsetGetMySavedTracks = 0;
    $limitGetMySavedTracks = 50;
    do {
        $retry = true;
        $nb_retry = 0;
        while ($retry) {
            try {
                // refresh api
                $api = getSpotifyWebAPI($w, $api);
                $userMySavedTracks = $api->getMySavedTracks(array(
                        'limit' => $limitGetMySavedTracks,
                        'offset' => $offsetGetMySavedTracks,
                        'market' => $country_code,
                    ));
                $retry = false;
            } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                logMsg('Error(getMySavedTracks): retry '.$nb_retry.' (exception '.print_r($e).')');

                if ($e->getCode() == 429) { // 429 is Too Many Requests
                $lastResponse = $api->getRequest()->getLastResponse();
                $retryAfter = $lastResponse['headers']['Retry-After'];
                sleep(retryAfter);
                } else if ($e->getCode() == 404) {
                    // skip
                    break;
                } else if ($e->getCode() == 500
                || $e->getCode() == 502 || $e->getCode() == 503) {
                // retry
                if ($nb_retry > 20) {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
                ++$nb_retry;
                sleep(15);
            } else {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
            }
        }

        foreach ($userMySavedTracks->items as $track) {
            $savedMySavedTracks[] = $track;
            $nb_tracktotal += 1;
        }

        $offsetGetMySavedTracks += $limitGetMySavedTracks;
    } while ($offsetGetMySavedTracks < $userMySavedTracks->total);

    // Handle playlists
    $w->write('Create Library▹0▹'.$nb_tracktotal.'▹'.$words[3].'▹'.'starting', 'update_library_in_progress');

    $nb_track = 0;

    try {
        $db->exec('create table tracks (yourmusic boolean, popularity int, uri text, album_uri text, artist_uri text, track_name text, album_name text, artist_name text, album_type text, track_artwork_path text, artist_artwork_path text, album_artwork_path text, playlist_name text, playlist_uri text, playable boolean, added_at text, duration text, nb_times_played int, local_track boolean)');
        $db->exec('CREATE INDEX IndexPlaylistUri ON tracks (playlist_uri)');
        $db->exec('CREATE INDEX IndexArtistName ON tracks (artist_name)');
        $db->exec('CREATE INDEX IndexAlbumName ON tracks (album_name)');
        $db->exec('create table counters (all_tracks int, yourmusic_tracks int, all_artists int, yourmusic_artists int, all_albums int, yourmusic_albums int, playlists int)');
        $db->exec('create table playlists (uri text PRIMARY KEY NOT NULL, name text, nb_tracks int, author text, username text, playlist_artwork_path text, ownedbyuser boolean, nb_playable_tracks int, duration_playlist text, nb_times_played int, collaborative boolean, public boolean)');

        $insertPlaylist = 'insert into playlists values (:uri,:name,:nb_tracks,:owner,:username,:playlist_artwork_path,:ownedbyuser,:nb_playable_tracks,:duration_playlist,:nb_times_played,:collaborative,:public)';
        $stmtPlaylist = $db->prepare($insertPlaylist);

        $insertTrack = 'insert into tracks values (:yourmusic,:popularity,:uri,:album_uri,:artist_uri,:track_name,:album_name,:artist_name,:album_type,:track_artwork_path,:artist_artwork_path,:album_artwork_path,:playlist_name,:playlist_uri,:playable,:added_at,:duration,:nb_times_played,:local_track)';
        $stmtTrack = $db->prepare($insertTrack);
    } catch (PDOException $e) {
        logMsg('Error(updateLibrary): (exception '.print_r($e).')');
        handleDbIssuePdoEcho($db, $w);
        $dbartworks = null;
        $db = null;

        return false;
    }

    if ($use_artworks) {
        try {
            // artworks
            $insertArtistArtwork = 'insert or ignore into artists values (:artist_uri, :artist_name,:already_fetched)';
            $stmtArtistArtwork = $dbartworks->prepare($insertArtistArtwork);

            $insertTrackArtwork = 'insert or ignore into tracks values (:track_uri,:already_fetched)';
            $stmtTrackArtwork = $dbartworks->prepare($insertTrackArtwork);

            $insertAlbumArtwork = 'insert or ignore into albums values (:album_uri,:already_fetched)';
            $stmtAlbumArtwork = $dbartworks->prepare($insertAlbumArtwork);
        } catch (PDOException $e) {
            logMsg('Error(updateLibrary): (exception '.print_r($e).')');
            handleDbIssuePdoEcho($dbartworks, $w);
            $dbartworks = null;
            $db = null;

            return false;
        }
        $artworksToDownload = false;
    }

    foreach ($savedListPlaylist as $playlist) {
        $duration_playlist = 0;
        $nb_track_playlist = 0;
        $tracks = $playlist->tracks;
        $owner = $playlist->owner;

        $playlist_artwork_path = getPlaylistArtwork($w, $playlist->uri, true, true, $use_artworks);

        if ('-'.$owner->id.'-' == '-'.$userid.'-') {
            $ownedbyuser = 1;
        } else {
            $ownedbyuser = 0;
        }

        try {
            $api = getSpotifyWebAPI($w);
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(getUserPlaylistTracks): playlist id '.$playlist->id.' (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);

            return false;
        }
        $offsetGetUserPlaylistTracks = 0;
        $limitGetUserPlaylistTracks = 100;
        do {
            $retry = true;
            $nb_retry = 0;
            while ($retry) {
                try {
                    // refresh api
                    $api = getSpotifyWebAPI($w, $api);
                    $userPlaylistTracks = $api->getUserPlaylistTracks(urlencode($owner->id), $playlist->id, array(
                            'fields' => array(
                                'total',
                                'items(added_at)',
                                'items(is_local)',
                                'items.track(is_playable,duration_ms,uri,popularity,name,linked_from)',
                                'items.track.album(album_type,images,uri,name)',
                                'items.track.artists(name,uri)',
                            ),
                            'limit' => $limitGetUserPlaylistTracks,
                            'offset' => $offsetGetUserPlaylistTracks,
                            'market' => $country_code,
                        ));
                    $retry = false;
                } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                    logMsg('Error(getUserPlaylists): retry '.$nb_retry.' (exception '.print_r($e).')');

                     if ($e->getCode() == 429) { // 429 is Too Many Requests
                        $lastResponse = $api->getRequest()->getLastResponse();
                        $retryAfter = $lastResponse['headers']['Retry-After'];
                        sleep(retryAfter);
                     } else if ($e->getCode() == 404) {
                            // skip
                            break;
                     } else if ($e->getCode() == 500
                        || $e->getCode() == 502 || $e->getCode() == 503) {
                        // retry
                        if ($nb_retry > 20) {
                            handleSpotifyWebAPIException($w, $e);
                            $retry = false;

                            return false;
                        }
                        ++$nb_retry;
                        sleep(15);
                    } else {
                        handleSpotifyWebAPIException($w, $e);
                        $retry = false;

                        return false;
                    }
                }
            }

            foreach ($userPlaylistTracks->items as $item) {
                $track = $item->track;
                $artists = $track->artists;
                $artist = $artists[0];
                $album = $track->album;

                $playable = 0;
                $local_track = 0;
                if (isset($track->is_playable) && $track->is_playable) {
                    $playable = 1;
                    if (isset($track->linked_from) && isset($track->linked_from->uri)) {
                        $track->uri = $track->linked_from->uri;
                    }
                }
                if (isset($item->is_local) && $item->is_local) {
                    $playable = 1;
                    $local_track = 1;
                }

                try {

                    // Download artworks in Fetch later mode
                    $thetrackuri = 'spotify:track:faketrackuri';
                    if ($local_track == 0 && isset($track->uri)) {
                        $thetrackuri = $track->uri;
                    }
                    if ($use_artworks) {
                        list($already_present, $track_artwork_path) = getTrackOrAlbumArtwork($w, $thetrackuri, true, true, false, $use_artworks);
                        if ($already_present == false) {
                            $artworksToDownload = true;
                            $stmtTrackArtwork->bindValue(':track_uri', $thetrackuri);
                            $stmtTrackArtwork->bindValue(':already_fetched', 0);
                            $stmtTrackArtwork->execute();
                        }
                    } else {
                        $track_artwork_path = getTrackOrAlbumArtwork($w, $thetrackuri, false, false, false, $use_artworks);
                    }

                    $theartistname = 'fakeartist';
                    if (isset($artist->name)) {
                        $theartistname = $artist->name;
                    }
                    $theartisturi = 'spotify:artist:fakeartisturi';
                    if (isset($artist->uri)) {
                        $theartisturi = $artist->uri;
                    }
                    if ($use_artworks) {
                        list($already_present, $artist_artwork_path) = getArtistArtwork($w, $theartisturi, $theartistname, true, true, false, $use_artworks);
                        if ($already_present == false) {
                            $artworksToDownload = true;
                            $stmtArtistArtwork->bindValue(':artist_uri', $artist->uri);
                            $stmtArtistArtwork->bindValue(':artist_name', $theartistname);
                            $stmtArtistArtwork->bindValue(':already_fetched', 0);
                            $stmtArtistArtwork->execute();
                        }
                    } else {
                        $artist_artwork_path = getArtistArtwork($w, $theartisturi, $theartistname, false, false, false, $use_artworks);
                    }

                    $thealbumuri = 'spotify:album:fakealbumuri';
                    if (isset($album->uri)) {
                        $thealbumuri = $album->uri;
                    }
                    if ($use_artworks) {
                        list($already_present, $album_artwork_path) = getTrackOrAlbumArtwork($w, $thealbumuri, true, true, false, $use_artworks);
                        if ($already_present == false) {
                            $artworksToDownload = true;
                            $stmtAlbumArtwork->bindValue(':album_uri', $thealbumuri);
                            $stmtAlbumArtwork->bindValue(':already_fetched', 0);
                            $stmtAlbumArtwork->execute();
                        }
                    } else {
                        $album_artwork_path = getTrackOrAlbumArtwork($w, $thealbumuri, false, false, false, $use_artworks);
                    }
                } catch (PDOException $e) {
                    logMsg('Error(updateLibrary): (exception '.print_r($e).')');
                    handleDbIssuePdoEcho($dbartworks, $w);
                    $dbartworks = null;
                    $db = null;

                    return false;
                }

                $duration_playlist += $track->duration_ms;

                try {
                    $stmtTrack->bindValue(':yourmusic', 0);
                    $stmtTrack->bindValue(':popularity', $track->popularity);
                    $stmtTrack->bindValue(':uri', $track->uri);
                    $stmtTrack->bindValue(':album_uri', $album->uri);
                    $stmtTrack->bindValue(':artist_uri', $artist->uri);
                    $stmtTrack->bindValue(':track_name', escapeQuery($track->name));
                    $stmtTrack->bindValue(':album_name', escapeQuery($album->name));
                    $stmtTrack->bindValue(':artist_name', escapeQuery($artist->name));
                    $stmtTrack->bindValue(':album_type', $album->album_type);
                    $stmtTrack->bindValue(':track_artwork_path', $track_artwork_path);
                    $stmtTrack->bindValue(':artist_artwork_path', $artist_artwork_path);
                    $stmtTrack->bindValue(':album_artwork_path', $album_artwork_path);
                    $stmtTrack->bindValue(':playlist_name', escapeQuery($playlist->name));
                    $stmtTrack->bindValue(':playlist_uri', $playlist->uri);
                    $stmtTrack->bindValue(':playable', $playable);
                    $stmtTrack->bindValue(':added_at', $item->added_at);
                    $stmtTrack->bindValue(':duration', beautifyTime($track->duration_ms / 1000));
                    $stmtTrack->bindValue(':nb_times_played', 0);
                    $stmtTrack->bindValue(':local_track', $local_track);
                    $stmtTrack->execute();
                } catch (PDOException $e) {
                    logMsg('Error(updateLibrary): (exception '.print_r($e).')');
                    handleDbIssuePdoEcho($db, $w);
                    $dbartworks = null;
                    $db = null;

                    return false;
                }
                ++$nb_track;
                ++$nb_track_playlist;
                if ($nb_track % 10 === 0) {
                    $w->write('Create Library▹'.$nb_track.'▹'.$nb_tracktotal.'▹'.$words[3].'▹'.escapeQuery($playlist->name), 'update_library_in_progress');
                }
            }

            $offsetGetUserPlaylistTracks += $limitGetUserPlaylistTracks;
        } while ($offsetGetUserPlaylistTracks < $userPlaylistTracks->total);

        try {
            $stmtPlaylist->bindValue(':uri', $playlist->uri);
            $stmtPlaylist->bindValue(':name', escapeQuery($playlist->name));
            $stmtPlaylist->bindValue(':nb_tracks', $playlist->tracks->total);
            $stmtPlaylist->bindValue(':owner', $owner->id);
            $stmtPlaylist->bindValue(':username', $owner->id);
            $stmtPlaylist->bindValue(':playlist_artwork_path', $playlist_artwork_path);
            $stmtPlaylist->bindValue(':ownedbyuser', $ownedbyuser);
            $stmtPlaylist->bindValue(':nb_playable_tracks', $nb_track_playlist);
            $stmtPlaylist->bindValue(':duration_playlist', beautifyTime($duration_playlist / 1000, true));
            $stmtPlaylist->bindValue(':nb_times_played', 0);
            $stmtPlaylist->bindValue(':collaborative', $playlist->collaborative);
            $stmtPlaylist->bindValue(':public', $playlist->public);
            $stmtPlaylist->execute();
        } catch (PDOException $e) {
            logMsg('Error(updateLibrary): (exception '.print_r($e).')');
            handleDbIssuePdoEcho($db, $w);
            $dbartworks = null;
            $db = null;

            return false;
        }
    }

    // Handle Your Music
    foreach ($savedMySavedTracks as $track) {
        $track = $track->track;
        $artists = $track->artists;
        $artist = $artists[0];
        $album = $track->album;

        $playable = 0;
        $local_track = 0;
        if (isset($track->is_playable) && $track->is_playable) {
            $playable = 1;
            if (isset($track->linked_from) && isset($track->linked_from->uri)) {
                $track->uri = $track->linked_from->uri;
            }
        }
        if (isset($item->is_local) && $item->is_local) {
            $playable = 1;
            $local_track = 1;
        }

        try {

            // Download artworks in Fetch later mode
            $thetrackuri = 'spotify:track:faketrackuri';
            if ($local_track == 0 && isset($track->uri)) {
                $thetrackuri = $track->uri;
            }
            if ($use_artworks) {
                list($already_present, $track_artwork_path) = getTrackOrAlbumArtwork($w, $thetrackuri, true, true, false, $use_artworks);
                if ($already_present == false) {
                    $artworksToDownload = true;
                    $stmtTrackArtwork->bindValue(':track_uri', $thetrackuri);
                    $stmtTrackArtwork->bindValue(':already_fetched', 0);
                    $stmtTrackArtwork->execute();
                }
            } else {
                $track_artwork_path = getTrackOrAlbumArtwork($w, $thetrackuri, false, false, false, $use_artworks);
            }

            $theartistname = 'fakeartist';
            if (isset($artist->name)) {
                $theartistname = $artist->name;
            }
            $theartisturi = 'spotify:artist:fakeartisturi';
            if (isset($artist->uri)) {
                $theartisturi = $artist->uri;
            }
            if ($use_artworks) {
                list($already_present, $artist_artwork_path) = getArtistArtwork($w, $theartisturi, $theartistname, true, true, false, $use_artworks);
                if ($already_present == false) {
                    $artworksToDownload = true;
                    $stmtArtistArtwork->bindValue(':artist_uri', $artist->uri);
                    $stmtArtistArtwork->bindValue(':artist_name', $theartistname);
                    $stmtArtistArtwork->bindValue(':already_fetched', 0);
                    $stmtArtistArtwork->execute();
                }
            } else {
                $artist_artwork_path = getArtistArtwork($w, $theartisturi, $theartistname, false, false, false, $use_artworks);
            }

            $thealbumuri = 'spotify:album:fakealbumuri';
            if (isset($album->uri)) {
                $thealbumuri = $album->uri;
            }
            if ($use_artworks) {
                list($already_present, $album_artwork_path) = getTrackOrAlbumArtwork($w, $thealbumuri, true, true, false, $use_artworks);
                if ($already_present == false) {
                    $artworksToDownload = true;
                    $stmtAlbumArtwork->bindValue(':album_uri', $thealbumuri);
                    $stmtAlbumArtwork->bindValue(':already_fetched', 0);
                    $stmtAlbumArtwork->execute();
                }
            } else {
                $album_artwork_path = getTrackOrAlbumArtwork($w, $thealbumuri, false, false, false, $use_artworks);
            }
        } catch (PDOException $e) {
            logMsg('Error(updateLibrary): (exception '.print_r($e).')');
            handleDbIssuePdoEcho($dbartworks, $w);
            $dbartworks = null;
            $db = null;

            return false;
        }

        try {
            $stmtTrack->bindValue(':yourmusic', 1);
            $stmtTrack->bindValue(':popularity', $track->popularity);
            $stmtTrack->bindValue(':uri', $track->uri);
            $stmtTrack->bindValue(':album_uri', $album->uri);
            $stmtTrack->bindValue(':artist_uri', $artist->uri);
            $stmtTrack->bindValue(':track_name', escapeQuery($track->name));
            $stmtTrack->bindValue(':album_name', escapeQuery($album->name));
            $stmtTrack->bindValue(':artist_name', escapeQuery($artist->name));
            $stmtTrack->bindValue(':album_type', $album->album_type);
            $stmtTrack->bindValue(':track_artwork_path', $track_artwork_path);
            $stmtTrack->bindValue(':artist_artwork_path', $artist_artwork_path);
            $stmtTrack->bindValue(':album_artwork_path', $album_artwork_path);
            $stmtTrack->bindValue(':playlist_name', '');
            $stmtTrack->bindValue(':playlist_uri', '');
            $stmtTrack->bindValue(':playable', $playable);
            $stmtTrack->bindValue(':added_at', $item->added_at);
            $stmtTrack->bindValue(':duration', beautifyTime($track->duration_ms / 1000));
            $stmtTrack->bindValue(':nb_times_played', 0);
            $stmtTrack->bindValue(':local_track', $local_track);
            $stmtTrack->execute();
        } catch (PDOException $e) {
            logMsg('Error(updateLibrary): (exception '.print_r($e).')');
            handleDbIssuePdoEcho($db, $w);
            $dbartworks = null;
            $db = null;

            return false;
        }

        ++$nb_track;
        if ($nb_track % 10 === 0) {
            $w->write('Create Library▹'.$nb_track.'▹'.$nb_tracktotal.'▹'.$words[3].'▹'.'Your Music', 'update_library_in_progress');
        }
    }

    // update counters
    try {
        $getCount = 'select count(distinct uri) from tracks';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $all_tracks = $stmt->fetch();

        $getCount = 'select count(distinct uri) from tracks where yourmusic=1';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $yourmusic_tracks = $stmt->fetch();

        $getCount = 'select count(distinct artist_name) from tracks';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $all_artists = $stmt->fetch();

        $getCount = 'select count(distinct artist_name) from tracks where yourmusic=1';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $yourmusic_artists = $stmt->fetch();

        $getCount = 'select count(distinct album_name) from tracks';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $all_albums = $stmt->fetch();

        $getCount = 'select count(distinct album_name) from tracks where yourmusic=1';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $yourmusic_albums = $stmt->fetch();

        $getCount = 'select count(*) from playlists';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $playlists_count = $stmt->fetch();

        $insertCounter = 'insert into counters values (:all_tracks,:yourmusic_tracks,:all_artists,:yourmusic_artists,:all_albums,:yourmusic_albums,:playlists)';
        $stmt = $db->prepare($insertCounter);

        $stmt->bindValue(':all_tracks', $all_tracks[0]);
        $stmt->bindValue(':yourmusic_tracks', $yourmusic_tracks[0]);
        $stmt->bindValue(':all_artists', $all_artists[0]);
        $stmt->bindValue(':yourmusic_artists', $yourmusic_artists[0]);
        $stmt->bindValue(':all_albums', $all_albums[0]);
        $stmt->bindValue(':yourmusic_albums', $yourmusic_albums[0]);
        $stmt->bindValue(':playlists', $playlists_count[0]);
        $stmt->execute();
    } catch (PDOException $e) {
        logMsg('Error(updateLibrary): (exception '.print_r($e).')');
        handleDbIssuePdoEcho($db, $w);
        $dbartworks = null;
        $db = null;

        return false;
    }

    $elapsed_time = time() - $words[3];
    if ($nb_skipped == 0) {
        displayNotificationWithArtwork($w, ' '.$nb_track.' tracks - took '.beautifyTime($elapsed_time, true), './images/recreate.png', 'Library (re-)created');
    } else {
        displayNotificationWithArtwork($w, ' '.$nb_track.' tracks / '.$nb_skipped.' skipped - took '.beautifyTime($elapsed_time, true), './images/recreate.png', 'Library (re-)created');
    }

    if (file_exists($w->data().'/library_old.db')) {
        deleteTheFile($w->data().'/library_old.db');
    }
    rename($w->data().'/library_new.db', $w->data().'/library.db');

    // remove legacy spotify app if needed
    if (file_exists(exec('printf $HOME').'/Spotify/spotify-app-miniplayer')) {
        exec('rm -rf '.exec('printf $HOME').'/Spotify/spotify-app-miniplayer');
    }
    // remove legacy settings.db if needed
    if (file_exists($w->data().'/settings.db')) {
        deleteTheFile($w->data().'/settings.db');
    }

    // Download artworks in background
    if ($use_artworks) {
        if ($artworksToDownload == true) {
            exec('php -f ./src/action.php -- "" "DOWNLOAD_ARTWORKS" "DOWNLOAD_ARTWORKS" >> "'.$w->cache().'/action.log" 2>&1 & ');
        }
    }
    deleteTheFile($w->data().'/update_library_in_progress');

    // in case of new user, force creation of links and current_user.json
    getCurrentUser($w);
}

/**
 * refreshLibrary function.
 *
 * @param mixed $w
 */
function refreshLibrary($w)
{
    if (!file_exists($w->data().'/library.db')) {
        displayNotificationWithArtwork($w, 'Refresh library called while library does not exist', './images/warning.png');

        return;
    }

    touch($w->data().'/update_library_in_progress');
    $w->write('InitRefreshLibrary▹'. 0 .'▹'. 0 .'▹'.time().'▹'.'starting', 'update_library_in_progress');

    $in_progress_data = $w->read('update_library_in_progress');

    // Read settings from JSON

    $settings = getSettings($w);

    $country_code = $settings->country_code;
    $userid = $settings->userid;
    $use_artworks = $settings->use_artworks;

    $words = explode('▹', $in_progress_data);

    putenv('LANG=fr_FR.UTF-8');

    ini_set('memory_limit', '512M');

    $nb_playlist = 0;

    if ($use_artworks) {
        // db for fetch artworks
        $fetch_artworks_existed = true;
        $dbfile = $w->data().'/fetch_artworks.db';
        if (!file_exists($dbfile)) {
            touch($dbfile);
            $fetch_artworks_existed = false;
        }
        // kill previous process if running
        $pid = exec("ps -efx | grep \"php\" | egrep \"DOWNLOAD_ARTWORKS\" | grep -v grep | awk '{print $2}'");
        if ($pid != '') {
            logMsg("KILL Download daemon <$pid>");
            $ret = exec("kill -9 \"$pid\"");
        }
        if (file_exists($w->data().'/download_artworks_in_progress')) {
            deleteTheFile($w->data().'/download_artworks_in_progress');
        }

        try {
            $dbartworks = new PDO("sqlite:$dbfile", '', '', array(
                    PDO::ATTR_PERSISTENT => true,
                ));
            $dbartworks->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
            handleDbIssuePdoEcho($dbartworks, $w);
            $dbartworks = null;
            $db = null;

            return false;
        }

        // DB artowrks
        if ($fetch_artworks_existed == false) {
            try {
                $dbartworks->exec('create table artists (artist_uri text PRIMARY KEY NOT NULL, artist_name text, already_fetched boolean)');
                $dbartworks->exec('create table tracks (track_uri text PRIMARY KEY NOT NULL, already_fetched boolean)');
                $dbartworks->exec('create table albums (album_uri text PRIMARY KEY NOT NULL, already_fetched boolean)');
            } catch (PDOException $e) {
                logMsg('Error(updateLibrary): (exception '.print_r($e).')');
                handleDbIssuePdoEcho($dbartworks, $w);
                $dbartworks = null;
                $db = null;

                return false;
            }
        }

        try {
            // artworks
            $insertArtistArtwork = 'insert or ignore into artists values (:artist_uri,:artist_name,:already_fetched)';
            $stmtArtistArtwork = $dbartworks->prepare($insertArtistArtwork);

            $insertTrackArtwork = 'insert or ignore into tracks values (:track_uri,:already_fetched)';
            $stmtTrackArtwork = $dbartworks->prepare($insertTrackArtwork);

            $insertAlbumArtwork = 'insert or ignore into albums values (:album_uri,:already_fetched)';
            $stmtAlbumArtwork = $dbartworks->prepare($insertAlbumArtwork);
        } catch (PDOException $e) {
            logMsg('Error(updateLibrary): (exception '.print_r($e).')');
            handleDbIssuePdoEcho($dbartworks, $w);
            $dbartworks = null;
            $db = null;

            return false;
        }
        $artworksToDownload = false;
    }

    rename($w->data().'/library.db', $w->data().'/library_old.db');
    copy($w->data().'/library_old.db', $w->data().'/library_new.db');
    $dbfile = $w->data().'/library_new.db';

    $nb_added_playlists = 0;
    $nb_removed_playlists = 0;
    $nb_updated_playlists = 0;

    try {
        $db = new PDO("sqlite:$dbfile", '', '', array(
                PDO::ATTR_PERSISTENT => true,
            ));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db->exec('drop table counters');
        $db->exec('create table counters (all_tracks int, yourmusic_tracks int, all_artists int, yourmusic_artists int, all_albums int, yourmusic_albums int, playlists int)');

        $getPlaylists = 'select * from playlists where uri=:uri';
        $stmtGetPlaylists = $db->prepare($getPlaylists);

        $insertPlaylist = 'insert into playlists values (:uri,:name,:nb_tracks,:owner,:username,:playlist_artwork_path,:ownedbyuser,:nb_playable_tracks,:duration_playlist,:nb_times_played,:collaborative,:public)';
        $stmtPlaylist = $db->prepare($insertPlaylist);

        $insertTrack = 'insert into tracks values (:yourmusic,:popularity,:uri,:album_uri,:artist_uri,:track_name,:album_name,:artist_name,:album_type,:track_artwork_path,:artist_artwork_path,:album_artwork_path,:playlist_name,:playlist_uri,:playable,:added_at,:duration,:nb_times_played,:local_track)';
        $stmtTrack = $db->prepare($insertTrack);

        $deleteFromTracks = 'delete from tracks where playlist_uri=:playlist_uri';
        $stmtDeleteFromTracks = $db->prepare($deleteFromTracks);

        $updatePlaylistsNbTracks = 'update playlists set nb_tracks=:nb_tracks,nb_playable_tracks=:nb_playable_tracks,duration_playlist=:duration_playlist,public=:public where uri=:uri';
        $stmtUpdatePlaylistsNbTracks = $db->prepare($updatePlaylistsNbTracks);

        $deleteFromTracksYourMusic = 'delete from tracks where yourmusic=:yourmusic';
        $stmtDeleteFromTracksYourMusic = $db->prepare($deleteFromTracksYourMusic);
    } catch (PDOException $e) {
        logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
        handleDbIssuePdoEcho($db, $w);
        $dbartworks = null;
        $db = null;

        return;
    }

    $savedListPlaylist = array();
    $offsetGetUserPlaylists = 0;
    $limitGetUserPlaylists = 50;
    do {
        $retry = true;
        $nb_retry = 0;
        try {
            $api = getSpotifyWebAPI($w);
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
            handleSpotifyWebAPIException($w, $e);

            return false;
        }

        while ($retry) {
            try {
                // refresh api
                $api = getSpotifyWebAPI($w, $api);
                $userPlaylists = $api->getUserPlaylists(urlencode($userid), array(
                        'limit' => $limitGetUserPlaylists,
                        'offset' => $offsetGetUserPlaylists,
                    ));
                $retry = false;
            } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                logMsg('Error(getUserPlaylists): retry '.$nb_retry.' (exception '.print_r($e).')');

                if ($e->getCode() == 429) { // 429 is Too Many Requests
                    $lastResponse = $api->getRequest()->getLastResponse();
                    $retryAfter = $lastResponse['headers']['Retry-After'];
                    sleep(retryAfter);
                } else if ($e->getCode() == 404) {
                        // skip
                        break;
                } else if ($e->getCode() == 500
                    || $e->getCode() == 502 || $e->getCode() == 503) {
                    // retry
                    if ($nb_retry > 20) {
                        handleSpotifyWebAPIException($w, $e);
                        $retry = false;

                        return false;
                    }
                    ++$nb_retry;
                    sleep(15);
                } else {
                    handleSpotifyWebAPIException($w, $e);
                    $retry = false;

                    return false;
                }
            }
        }
        $nb_playlist_total = $userPlaylists->total;

        foreach ($userPlaylists->items as $playlist) {
            if ($playlist->name != '') {
                $savedListPlaylist[] = $playlist;
            }
        }
        $offsetGetUserPlaylists += $limitGetUserPlaylists;
    } while ($offsetGetUserPlaylists < $userPlaylists->total);

    // consider Your Music as a playlist for progress bar
    ++$nb_playlist_total;

    foreach ($savedListPlaylist as $playlist) {
        $tracks = $playlist->tracks;
        $owner = $playlist->owner;

        ++$nb_playlist;
        $w->write('Refresh Library▹'.$nb_playlist.'▹'.$nb_playlist_total.'▹'.$words[3].'▹'.escapeQuery($playlist->name), 'update_library_in_progress');

        try {
            // Loop on existing playlists in library
            $stmtGetPlaylists->bindValue(':uri', $playlist->uri);
            $stmtGetPlaylists->execute();

            $noresult = true;
            while ($playlists = $stmtGetPlaylists->fetch()) {
                $noresult = false;
                break;
            }
        } catch (PDOException $e) {
            logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
            handleDbIssuePdoEcho($db, $w);
            $dbartworks = null;
            $db = null;

            return;
        }

        // Playlist does not exist, add it
        if ($noresult == true) {
            ++$nb_added_playlists;
            $playlist_artwork_path = getPlaylistArtwork($w, $playlist->uri, true, true, $use_artworks);

            if ('-'.$owner->id.'-' == '-'.$userid.'-') {
                $ownedbyuser = 1;
            } else {
                $ownedbyuser = 0;
            }

            $nb_track_playlist = 0;
            $duration_playlist = 0;
            $offsetGetUserPlaylistTracks = 0;
            $limitGetUserPlaylistTracks = 100;
            do {
                $retry = true;
                $nb_retry = 0;
                while ($retry) {
                    try {
                        // refresh api
                        $api = getSpotifyWebAPI($w, $api);
                        $userPlaylistTracks = $api->getUserPlaylistTracks(urlencode($owner->id), $playlist->id, array(
                                'fields' => array(
                                    'total',
                                    'items(added_at)',
                                    'items(is_local)',
                                    'items.track(is_playable,duration_ms,uri,popularity,name,linked_from)',
                                    'items.track.album(album_type,images,uri,name)',
                                    'items.track.artists(name,uri)',
                                ),
                                'limit' => $limitGetUserPlaylistTracks,
                                'offset' => $offsetGetUserPlaylistTracks,
                                'market' => $country_code,
                            ));
                        $retry = false;
                    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                        logMsg('Error(getUserPlaylistTracks): retry '.$nb_retry.' (exception '.print_r($e).')');

                        if ($e->getCode() == 429) { // 429 is Too Many Requests
                            $lastResponse = $api->getRequest()->getLastResponse();
                            $retryAfter = $lastResponse['headers']['Retry-After'];
                            sleep(retryAfter);
                        } else if ($e->getCode() == 404) {
                                // skip
                                break;
                        } else if ($e->getCode() == 500
                            || $e->getCode() == 502 || $e->getCode() == 503) {
                            // retry
                            if ($nb_retry > 20) {
                                handleSpotifyWebAPIException($w, $e);
                                $retry = false;

                                return false;
                            }
                            ++$nb_retry;
                            sleep(15);
                        } else {
                            handleSpotifyWebAPIException($w, $e);
                            $retry = false;

                            return false;
                        }
                    }
                }

                foreach ($userPlaylistTracks->items as $item) {
                    $track = $item->track;
                    $artists = $track->artists;
                    $artist = $artists[0];
                    $album = $track->album;

                    $playable = 0;
                    $local_track = 0;
                    if (isset($track->is_playable) && $track->is_playable) {
                        $playable = 1;
                        if (isset($track->linked_from) && isset($track->linked_from->uri)) {
                            $track->uri = $track->linked_from->uri;
                        }
                    }
                    if (isset($item->is_local) && $item->is_local) {
                        $playable = 1;
                        $local_track = 1;
                    }

                    try {

                        // Download artworks in Fetch later mode
                        $thetrackuri = 'spotify:track:faketrackuri';
                        if ($local_track == 0 && isset($track->uri)) {
                            $thetrackuri = $track->uri;
                        }
                        if ($use_artworks) {
                            list($already_present, $track_artwork_path) = getTrackOrAlbumArtwork($w, $thetrackuri, true, true, false, $use_artworks);
                            if ($already_present == false) {
                                $artworksToDownload = true;
                                $stmtTrackArtwork->bindValue(':track_uri', $thetrackuri);
                                $stmtTrackArtwork->bindValue(':already_fetched', 0);
                                $stmtTrackArtwork->execute();
                            }
                        } else {
                            $track_artwork_path = getTrackOrAlbumArtwork($w, $thetrackuri, false, false, false, $use_artworks);
                        }
                        $theartistname = 'fakeartist';
                        if (isset($artist->name)) {
                            $theartistname = $artist->name;
                        }
                        $theartisturi = 'spotify:artist:fakeartisturi';
                        if (isset($artist->uri)) {
                            $theartisturi = $artist->uri;
                        }
                        if ($use_artworks) {
                            list($already_present, $artist_artwork_path) = getArtistArtwork($w, $theartisturi, $theartistname, true, true, false, $use_artworks);
                            if ($already_present == false) {
                                $artworksToDownload = true;
                                $stmtArtistArtwork->bindValue(':artist_uri', $artist->uri);
                                $stmtArtistArtwork->bindValue(':artist_name', $theartistname);
                                $stmtArtistArtwork->bindValue(':already_fetched', 0);
                                $stmtArtistArtwork->execute();
                            }
                        } else {
                            $artist_artwork_path = getArtistArtwork($w, $theartisturi, $theartistname, false, false, false, $use_artworks);
                        }

                        $thealbumuri = 'spotify:album:fakealbumuri';
                        if (isset($album->uri)) {
                            $thealbumuri = $album->uri;
                        }
                        if ($use_artworks) {
                            list($already_present, $album_artwork_path) = getTrackOrAlbumArtwork($w, $thealbumuri, true, true, false, $use_artworks);
                            if ($already_present == false) {
                                $artworksToDownload = true;
                                $stmtAlbumArtwork->bindValue(':album_uri', $thealbumuri);
                                $stmtAlbumArtwork->bindValue(':already_fetched', 0);
                                $stmtAlbumArtwork->execute();
                            }
                        } else {
                            $album_artwork_path = getTrackOrAlbumArtwork($w, $thealbumuri, false, false, false, $use_artworks);
                        }
                    } catch (PDOException $e) {
                        logMsg('Error(updateLibrary): (exception '.print_r($e).')');
                        handleDbIssuePdoEcho($dbartworks, $w);
                        $dbartworks = null;
                        $db = null;

                        return false;
                    }

                    $duration_playlist += $track->duration_ms;

                    try {
                        $stmtTrack->bindValue(':yourmusic', 0);
                        $stmtTrack->bindValue(':popularity', $track->popularity);
                        $stmtTrack->bindValue(':uri', $track->uri);
                        $stmtTrack->bindValue(':album_uri', $album->uri);
                        $stmtTrack->bindValue(':artist_uri', $artist->uri);
                        $stmtTrack->bindValue(':track_name', escapeQuery($track->name));
                        $stmtTrack->bindValue(':album_name', escapeQuery($album->name));
                        $stmtTrack->bindValue(':artist_name', escapeQuery($artist->name));
                        $stmtTrack->bindValue(':album_type', $album->album_type);
                        $stmtTrack->bindValue(':track_artwork_path', $track_artwork_path);
                        $stmtTrack->bindValue(':artist_artwork_path', $artist_artwork_path);
                        $stmtTrack->bindValue(':album_artwork_path', $album_artwork_path);
                        $stmtTrack->bindValue(':playlist_name', escapeQuery($playlist->name));
                        $stmtTrack->bindValue(':playlist_uri', $playlist->uri);
                        $stmtTrack->bindValue(':playable', $playable);
                        $stmtTrack->bindValue(':added_at', $item->added_at);
                        $stmtTrack->bindValue(':duration', beautifyTime($track->duration_ms / 1000));
                        $stmtTrack->bindValue(':nb_times_played', 0);
                        $stmtTrack->bindValue(':local_track', $local_track);
                        $stmtTrack->execute();
                    } catch (PDOException $e) {
                        logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
                        handleDbIssuePdoEcho($db, $w);
                        $dbartworks = null;
                        $db = null;

                        return;
                    }
                    ++$nb_track_playlist;
                }

                $offsetGetUserPlaylistTracks += $limitGetUserPlaylistTracks;
            } while ($offsetGetUserPlaylistTracks < $userPlaylistTracks->total);

            try {
                $stmtPlaylist->bindValue(':uri', $playlist->uri);
                $stmtPlaylist->bindValue(':name', escapeQuery($playlist->name));
                $stmtPlaylist->bindValue(':nb_tracks', $tracks->total);
                $stmtPlaylist->bindValue(':owner', $owner->id);
                $stmtPlaylist->bindValue(':username', $owner->id);
                $stmtPlaylist->bindValue(':playlist_artwork_path', $playlist_artwork_path);
                $stmtPlaylist->bindValue(':ownedbyuser', $ownedbyuser);
                $stmtPlaylist->bindValue(':nb_playable_tracks', $nb_track_playlist);
                $stmtPlaylist->bindValue(':duration_playlist', beautifyTime($duration_playlist / 1000, true));
                $stmtPlaylist->bindValue(':nb_times_played', 0);
                $stmtPlaylist->bindValue(':collaborative', $playlist->collaborative);
                $stmtPlaylist->bindValue(':public', $playlist->public);
                $stmtPlaylist->execute();
            } catch (PDOException $e) {
                logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
                handleDbIssuePdoEcho($db, $w);
                $dbartworks = null;
                $db = null;

                return;
            }

            displayNotificationWithArtwork($w, 'Added playlist '.escapeQuery($playlist->name), $playlist_artwork_path, 'Refresh Library');
        } else {

            // check if this is a self-updated playlist (spotify and 30 tracks)
            $selfUpdatedPlaylistUpdated = false;
            $tmp = explode(':',$playlists[0]);
            if($tmp[2] == 'spotify' && $tracks->total == 30) {

                try {
                    $getOneTrack = 'select added_at from tracks where playlist_uri=:theplaylisturi order by added_at desc limit 1';
                    $stmtGetOneTrack = $db->prepare($getOneTrack);
                    $stmtGetOneTrack->bindValue(':theplaylisturi', $playlists[0]);
                    $stmtGetOneTrack->execute();
                    $theOneTrack = $stmtGetOneTrack->fetch();
                    date_default_timezone_set('UTC');
                    $today = date("c");
                    $last_updated  = $theOneTrack[0];
                    $today_time = strtotime($today);
                    $last_updated_time = strtotime($last_updated);

                    if( ($today_time - $last_updated_time) > 7*24*3600) {
                        $selfUpdatedPlaylistUpdated = true;   
                    }
                } catch (PDOException $e) {
                    logMsg('Error(refreshLibrary - self-updated playlist): (exception '.print_r($e).')');
                    handleDbIssuePdoEcho($db, $w);
                    $dbartworks = null;
                    $db = null;

                    return;
                }
            }

            // number of tracks has changed or playlist name has changed or the privacy has changed, or spotify playlist (Release Radar, Discover Weekly)
            // update the playlist
            if ($selfUpdatedPlaylistUpdated || $playlists[2] != $tracks->total || $playlists[1] != escapeQuery($playlist->name) ||
                (($playlists[11] == '' && $playlist->public == true) || ($playlists[11] == true && $playlist->public == ''))) {
                ++$nb_updated_playlists;

                // force refresh of playlist artwork
                getPlaylistArtwork($w, $playlist->uri, true, true, $use_artworks);

                try {
                    if ($playlists[1] != escapeQuery($playlist->name)) {
                        $updatePlaylistsName = 'update playlists set name=:name where uri=:uri';
                        $stmtUpdatePlaylistsName = $db->prepare($updatePlaylistsName);

                        $stmtUpdatePlaylistsName->bindValue(':name', escapeQuery($playlist->name));
                        $stmtUpdatePlaylistsName->bindValue(':uri', $playlist->uri);
                        $stmtUpdatePlaylistsName->execute();
                    }

                    $stmtDeleteFromTracks->bindValue(':playlist_uri', $playlist->uri);
                    $stmtDeleteFromTracks->execute();
                } catch (PDOException $e) {
                    logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
                    handleDbIssuePdoEcho($db, $w);
                    $dbartworks = null;
                    $db = null;

                    return;
                }

                $duration_playlist = 0;
                $nb_track_playlist = 0;
                $offsetGetUserPlaylistTracks = 0;
                $limitGetUserPlaylistTracks = 100;
                $owner = $playlist->owner;
                do {
                    $retry = true;
                    $nb_retry = 0;
                    while ($retry) {
                        try {
                            // refresh api
                            $api = getSpotifyWebAPI($w, $api);
                            $userPlaylistTracks = $api->getUserPlaylistTracks(urlencode($owner->id), $playlist->id, array(
                                    'fields' => array(
                                        'total',
                                        'items(added_at)',
                                        'items(is_local)',
                                        'items.track(is_playable,duration_ms,uri,popularity,name,linked_from)',
                                        'items.track.album(album_type,images,uri,name)',
                                        'items.track.artists(name,uri)',
                                    ),
                                    'limit' => $limitGetUserPlaylistTracks,
                                    'offset' => $offsetGetUserPlaylistTracks,
                                    'market' => $country_code,
                                ));
                            $retry = false;
                        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                            logMsg('Error(getUserPlaylistTracks): retry '.$nb_retry.' (exception '.print_r($e).')');

                            if ($e->getCode() == 429) { // 429 is Too Many Requests
                                $lastResponse = $api->getRequest()->getLastResponse();
                                $retryAfter = $lastResponse['headers']['Retry-After'];
                                sleep(retryAfter);
                            } else if ($e->getCode() == 404) {
                                    // skip
                                    break;
                            } else if ($e->getCode() == 500
                                || $e->getCode() == 502 || $e->getCode() == 503) {
                                // retry
                                if ($nb_retry > 20) {
                                    handleSpotifyWebAPIException($w, $e);
                                    $retry = false;

                                    return false;
                                }
                                ++$nb_retry;
                                sleep(15);
                            } else {
                                handleSpotifyWebAPIException($w, $e);
                                $retry = false;

                                return false;
                            }
                        }
                    }

                    foreach ($userPlaylistTracks->items as $item) {
                        $track = $item->track;
                        $artists = $track->artists;
                        $artist = $artists[0];
                        $album = $track->album;

                        $playable = 0;
                        $local_track = 0;
                        if (isset($track->is_playable) && $track->is_playable) {
                            $playable = 1;
                            if (isset($track->linked_from) && isset($track->linked_from->uri)) {
                                $track->uri = $track->linked_from->uri;
                            }
                        }
                        if (isset($item->is_local) && $item->is_local) {
                            $playable = 1;
                            $local_track = 1;
                        }

                        try {

                            // Download artworks in Fetch later mode
                            $thetrackuri = 'spotify:track:faketrackuri';
                            if ($local_track == 0 && isset($track->uri)) {
                                $thetrackuri = $track->uri;
                            }

                            if ($use_artworks) {
                                list($already_present, $track_artwork_path) = getTrackOrAlbumArtwork($w, $thetrackuri, true, true, false, $use_artworks);
                                if ($already_present == false) {
                                    $artworksToDownload = true;
                                    $stmtTrackArtwork->bindValue(':track_uri', $thetrackuri);
                                    $stmtTrackArtwork->bindValue(':already_fetched', 0);
                                    $stmtTrackArtwork->execute();
                                }
                            } else {
                                $track_artwork_path = getTrackOrAlbumArtwork($w, $thetrackuri, false, false, false, $use_artworks);
                            }

                            $theartistname = 'fakeartist';
                            if (isset($artist->name)) {
                                $theartistname = $artist->name;
                            }
                            $theartisturi = 'spotify:artist:fakeartisturi';
                            if (isset($artist->uri)) {
                                $theartisturi = $artist->uri;
                            }
                            if ($use_artworks) {
                                list($already_present, $artist_artwork_path) = getArtistArtwork($w, $theartisturi, $theartistname, true, true, false, $use_artworks);
                                if ($already_present == false) {
                                    $artworksToDownload = true;
                                    $stmtArtistArtwork->bindValue(':artist_uri', $artist->uri);
                                    $stmtArtistArtwork->bindValue(':artist_name', $theartistname);
                                    $stmtArtistArtwork->bindValue(':already_fetched', 0);
                                    $stmtArtistArtwork->execute();
                                }
                            } else {
                                $artist_artwork_path = getArtistArtwork($w, $theartisturi, $theartistname, false, false, false, $use_artworks);
                            }

                            $thealbumuri = 'spotify:album:fakealbumuri';
                            if (isset($album->uri)) {
                                $thealbumuri = $album->uri;
                            }
                            if ($use_artworks) {
                                list($already_present, $album_artwork_path) = getTrackOrAlbumArtwork($w, $thealbumuri, true, true, false, $use_artworks);
                                if ($already_present == false) {
                                    $artworksToDownload = true;
                                    $stmtAlbumArtwork->bindValue(':album_uri', $thealbumuri);
                                    $stmtAlbumArtwork->bindValue(':already_fetched', 0);
                                    $stmtAlbumArtwork->execute();
                                }
                            } else {
                                $album_artwork_path = getTrackOrAlbumArtwork($w, $thealbumuri, false, false, false, $use_artworks);
                            }
                        } catch (PDOException $e) {
                            logMsg('Error(updateLibrary): (exception '.print_r($e).')');
                            handleDbIssuePdoEcho($dbartworks, $w);
                            $dbartworks = null;
                            $db = null;

                            return false;
                        }

                        $duration_playlist += $track->duration_ms;
                        try {
                            $stmtTrack->bindValue(':yourmusic', 0);
                            $stmtTrack->bindValue(':popularity', $track->popularity);
                            $stmtTrack->bindValue(':uri', $track->uri);
                            $stmtTrack->bindValue(':album_uri', $album->uri);
                            $stmtTrack->bindValue(':artist_uri', $artist->uri);
                            $stmtTrack->bindValue(':track_name', escapeQuery($track->name));
                            $stmtTrack->bindValue(':album_name', escapeQuery($album->name));
                            $stmtTrack->bindValue(':artist_name', escapeQuery($artist->name));
                            $stmtTrack->bindValue(':album_type', $album->album_type);
                            $stmtTrack->bindValue(':track_artwork_path', $track_artwork_path);
                            $stmtTrack->bindValue(':artist_artwork_path', $artist_artwork_path);
                            $stmtTrack->bindValue(':album_artwork_path', $album_artwork_path);
                            $stmtTrack->bindValue(':playlist_name', escapeQuery($playlist->name));
                            $stmtTrack->bindValue(':playlist_uri', $playlist->uri);
                            $stmtTrack->bindValue(':playable', $playable);
                            $stmtTrack->bindValue(':added_at', $item->added_at);
                            $stmtTrack->bindValue(':duration', beautifyTime($track->duration_ms / 1000));
                            $stmtTrack->bindValue(':nb_times_played', 0);
                            $stmtTrack->bindValue(':local_track', $local_track);
                            $stmtTrack->execute();
                        } catch (PDOException $e) {
                            logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
                            handleDbIssuePdoEcho($db, $w);
                            $dbartworks = null;
                            $db = null;

                            return;
                        }
                        ++$nb_track_playlist;
                    }

                    $offsetGetUserPlaylistTracks += $limitGetUserPlaylistTracks;
                } while ($offsetGetUserPlaylistTracks < $userPlaylistTracks->total);

                try {
                    $stmtUpdatePlaylistsNbTracks->bindValue(':nb_tracks', $userPlaylistTracks->total);
                    $stmtUpdatePlaylistsNbTracks->bindValue(':nb_playable_tracks', $nb_track_playlist);
                    $stmtUpdatePlaylistsNbTracks->bindValue(':duration_playlist', beautifyTime($duration_playlist / 1000, true));
                    $stmtUpdatePlaylistsNbTracks->bindValue(':uri', $playlist->uri);
                    $stmtUpdatePlaylistsNbTracks->bindValue(':public', $playlist->public);
                    $stmtUpdatePlaylistsNbTracks->execute();
                } catch (PDOException $e) {
                    logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
                    handleDbIssuePdoEcho($db, $w);
                    $dbartworks = null;
                    $db = null;

                    return;
                }
                displayNotificationWithArtwork($w, 'Updated playlist '.escapeQuery($playlist->name), getPlaylistArtwork($w, $playlist->uri, true, false, $use_artworks), 'Refresh Library');
            } else {
                continue;
            }
        }
    }

    try {
        // check for deleted playlists
        $getPlaylists = 'select * from playlists';
        $stmt = $db->prepare($getPlaylists);
        $stmt->execute();

        while ($playlist_in_db = $stmt->fetch()) {
            $found = false;
            foreach ($savedListPlaylist as $playlist) {
                if ($playlist->uri == $playlist_in_db[0]) {
                    $found = true;
                    break;
                }
            }
            if ($found == false) {
                ++$nb_removed_playlists;

                $deleteFromPlaylist = 'delete from playlists where uri=:uri';
                $stmtDelete = $db->prepare($deleteFromPlaylist);
                $stmtDelete->bindValue(':uri', $playlist_in_db[0]);
                $stmtDelete->execute();

                $deleteFromTracks = 'delete from tracks where playlist_uri=:uri';
                $stmtDelete = $db->prepare($deleteFromTracks);
                $stmtDelete->bindValue(':uri', $playlist_in_db[0]);
                $stmtDelete->execute();
                displayNotificationWithArtwork($w, 'Removed playlist '.$playlist_in_db[1], getPlaylistArtwork($w, $playlist_in_db[0], false, false, $use_artworks), 'Refresh Library');
            }
        }
    } catch (PDOException $e) {
        logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
        handleDbIssuePdoEcho($db, $w);
        $dbartworks = null;
        $db = null;

        return;
    }

    // check for update to Your Music
    $retry = true;
    $nb_retry = 0;
    while ($retry) {
        try {
            // refresh api
            $api = getSpotifyWebAPI($w, $api);

            // get only one, we just want to check total for now
            $userMySavedTracks = $api->getMySavedTracks(array(
                    'limit' => 1,
                    'offset' => 0,
                ));
            $retry = false;
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            logMsg('Error(getMySavedTracks): retry '.$nb_retry.' (exception '.print_r($e).')');

            if ($e->getCode() == 429) { // 429 is Too Many Requests
            $lastResponse = $api->getRequest()->getLastResponse();
            $retryAfter = $lastResponse['headers']['Retry-After'];
            sleep(retryAfter);
            } else if ($e->getCode() == 404) {
                // skip
                break;
            } else if ($e->getCode() == 500
            || $e->getCode() == 502 || $e->getCode() == 503) {
            // retry
            if ($nb_retry > 20) {
                handleSpotifyWebAPIException($w, $e);
                $retry = false;

                return false;
            }
            ++$nb_retry;
            sleep(15);
        } else {
            handleSpotifyWebAPIException($w, $e);
            $retry = false;

            return false;
        }
        }
    }

    try {
        // get current number of track in Your Music
        $getCount = 'select count(distinct uri) from tracks where yourmusic=1';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $yourmusic_tracks = $stmt->fetch();
    } catch (PDOException $e) {
        logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
        handleDbIssuePdoEcho($db, $w);
        $db = null;

        return;
    }

    $your_music_updated = false;
    if ($yourmusic_tracks[0] != $userMySavedTracks->total) {
        $your_music_updated = true;
        // Your Music has changed, update it
        ++$nb_playlist;
        $w->write('Refresh Library▹'.$nb_playlist.'▹'.$nb_playlist_total.'▹'.$words[3].'▹'.'Your Music', 'update_library_in_progress');

        // delete tracks
        try {
            $stmtDeleteFromTracksYourMusic->bindValue(':yourmusic', 1);
            $stmtDeleteFromTracksYourMusic->execute();
        } catch (PDOException $e) {
            logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
            handleDbIssuePdoEcho($db, $w);
            $db = null;

            return;
        }

        $offsetGetMySavedTracks = 0;
        $limitGetMySavedTracks = 50;
        do {
            $retry = true;
            $nb_retry = 0;
            while ($retry) {
                try {
                    // refresh api
                    $api = getSpotifyWebAPI($w, $api);
                    $userMySavedTracks = $api->getMySavedTracks(array(
                            'limit' => $limitGetMySavedTracks,
                            'offset' => $offsetGetMySavedTracks,
                            'market' => $country_code,
                        ));
                    $retry = false;
                } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
                    logMsg('Error(getMySavedTracks): retry '.$nb_retry.' (exception '.print_r($e).')');

                     if ($e->getCode() == 429) { // 429 is Too Many Requests
                        $lastResponse = $api->getRequest()->getLastResponse();
                        $retryAfter = $lastResponse['headers']['Retry-After'];
                        sleep(retryAfter);
                     } else if ($e->getCode() == 404) {
                            // skip
                            break;
                     } else if ($e->getCode() == 500
                        || $e->getCode() == 502 || $e->getCode() == 503) {
                        // retry
                        if ($nb_retry > 20) {
                            handleSpotifyWebAPIException($w, $e);
                            $retry = false;

                            return false;
                        }
                        ++$nb_retry;
                        sleep(15);
                    } else {
                        handleSpotifyWebAPIException($w, $e);
                        $retry = false;

                        return false;
                    }
                }
            }

            foreach ($userMySavedTracks->items as $item) {
                $track = $item->track;
                $artists = $track->artists;
                $artist = $artists[0];
                $album = $track->album;

                $playable = 0;
                $local_track = 0;
                if (isset($track->is_playable) && $track->is_playable) {
                    $playable = 1;
                    if (isset($track->linked_from) && isset($track->linked_from->uri)) {
                        $track->uri = $track->linked_from->uri;
                    }
                }
                if (isset($item->is_local) && $item->is_local) {
                    $playable = 1;
                    $local_track = 1;
                }

                try {

                    // Download artworks in Fetch later mode
                    $thetrackuri = 'spotify:track:faketrackuri';
                    if ($local_track == 0 && isset($track->uri)) {
                        $thetrackuri = $track->uri;
                    }
                    if ($use_artworks) {
                        list($already_present, $track_artwork_path) = getTrackOrAlbumArtwork($w, $thetrackuri, true, true, false, $use_artworks);
                        if ($already_present == false) {
                            $artworksToDownload = true;
                            $stmtTrackArtwork->bindValue(':track_uri', $thetrackuri);
                            $stmtTrackArtwork->bindValue(':already_fetched', 0);
                            $stmtTrackArtwork->execute();
                        }
                    } else {
                        $track_artwork_path = getTrackOrAlbumArtwork($w, $thetrackuri, false, false, false, $use_artworks);
                    }

                    $theartistname = 'fakeartist';
                    if (isset($artist->name)) {
                        $theartistname = $artist->name;
                    }
                    $theartisturi = 'spotify:artist:fakeartisturi';
                    if (isset($artist->uri)) {
                        $theartisturi = $artist->uri;
                    }
                    if ($use_artworks) {
                        list($already_present, $artist_artwork_path) = getArtistArtwork($w, $theartisturi, $theartistname, true, true, false, $use_artworks);
                        if ($already_present == false) {
                            $artworksToDownload = true;
                            $stmtArtistArtwork->bindValue(':artist_uri', $artist->uri);
                            $stmtArtistArtwork->bindValue(':artist_name', $theartistname);
                            $stmtArtistArtwork->bindValue(':already_fetched', 0);
                            $stmtArtistArtwork->execute();
                        }
                    } else {
                        $artist_artwork_path = getArtistArtwork($w, $theartisturi, $theartistname, false, false, false, false, $use_artworks);
                    }

                    $thealbumuri = 'spotify:album:fakealbumuri';
                    if (isset($album->uri)) {
                        $thealbumuri = $album->uri;
                    }
                    if ($use_artworks) {
                        list($already_present, $album_artwork_path) = getTrackOrAlbumArtwork($w, $thealbumuri, true, true, false, $use_artworks);
                        if ($already_present == false) {
                            $artworksToDownload = true;
                            $stmtAlbumArtwork->bindValue(':album_uri', $thealbumuri);
                            $stmtAlbumArtwork->bindValue(':already_fetched', 0);
                            $stmtAlbumArtwork->execute();
                        }
                    } else {
                        $album_artwork_path = getTrackOrAlbumArtwork($w, $thealbumuri, false, false, false, $use_artworks);
                    }
                } catch (PDOException $e) {
                    logMsg('Error(updateLibrary): (exception '.print_r($e).')');
                    handleDbIssuePdoEcho($dbartworks, $w);
                    $dbartworks = null;
                    $db = null;

                    return false;
                }

                try {
                    $stmtTrack->bindValue(':yourmusic', 1);
                    $stmtTrack->bindValue(':popularity', $track->popularity);
                    $stmtTrack->bindValue(':uri', $track->uri);
                    $stmtTrack->bindValue(':album_uri', $album->uri);
                    $stmtTrack->bindValue(':artist_uri', $artist->uri);
                    $stmtTrack->bindValue(':track_name', escapeQuery($track->name));
                    $stmtTrack->bindValue(':album_name', escapeQuery($album->name));
                    $stmtTrack->bindValue(':artist_name', escapeQuery($artist->name));
                    $stmtTrack->bindValue(':album_type', $album->album_type);
                    $stmtTrack->bindValue(':track_artwork_path', $track_artwork_path);
                    $stmtTrack->bindValue(':artist_artwork_path', $artist_artwork_path);
                    $stmtTrack->bindValue(':album_artwork_path', $album_artwork_path);
                    $stmtTrack->bindValue(':playlist_name', '');
                    $stmtTrack->bindValue(':playlist_uri', '');
                    $stmtTrack->bindValue(':playable', $playable);
                    $stmtTrack->bindValue(':added_at', $item->added_at);
                    $stmtTrack->bindValue(':duration', beautifyTime($track->duration_ms / 1000));
                    $stmtTrack->bindValue(':nb_times_played', 0);
                    $stmtTrack->bindValue(':local_track', $local_track);
                    $stmtTrack->execute();
                } catch (PDOException $e) {
                    logMsg('Error(refreshLibrary): (exception '.print_r($e).')');
                    handleDbIssuePdoEcho($db, $w);
                    $db = null;

                    return;
                }
            }

            $offsetGetMySavedTracks += $limitGetMySavedTracks;
        } while ($offsetGetMySavedTracks < $userMySavedTracks->total);
    }

    // update counters
    try {
        $getCount = 'select count(distinct uri) from tracks';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $all_tracks = $stmt->fetch();

        $getCount = 'select count(distinct uri) from tracks where yourmusic=1';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $yourmusic_tracks = $stmt->fetch();

        $getCount = 'select count(distinct artist_name) from tracks';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $all_artists = $stmt->fetch();

        $getCount = 'select count(distinct artist_name) from tracks where yourmusic=1';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $yourmusic_artists = $stmt->fetch();

        $getCount = 'select count(distinct album_name) from tracks';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $all_albums = $stmt->fetch();

        $getCount = 'select count(distinct album_name) from tracks where yourmusic=1';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $yourmusic_albums = $stmt->fetch();

        $getCount = 'select count(*) from playlists';
        $stmt = $db->prepare($getCount);
        $stmt->execute();
        $playlists_count = $stmt->fetch();

        $insertCounter = 'insert into counters values (:all_tracks,:yourmusic_tracks,:all_artists,:yourmusic_artists,:all_albums,:yourmusic_albums,:playlists)';
        $stmt = $db->prepare($insertCounter);

        $stmt->bindValue(':all_tracks', $all_tracks[0]);
        $stmt->bindValue(':yourmusic_tracks', $yourmusic_tracks[0]);
        $stmt->bindValue(':all_artists', $all_artists[0]);
        $stmt->bindValue(':yourmusic_artists', $yourmusic_artists[0]);
        $stmt->bindValue(':all_albums', $all_albums[0]);
        $stmt->bindValue(':yourmusic_albums', $yourmusic_albums[0]);
        $stmt->bindValue(':playlists', $playlists_count[0]);
        $stmt->execute();
    } catch (PDOException $e) {
        logMsg('Error(updateLibrary): (exception '.print_r($e).')');
        handleDbIssuePdoEcho($db, $w);
        $dbartworks = null;
        $db = null;

        return false;
    }

    $elapsed_time = time() - $words[3];
    $changedPlaylists = false;
    $changedYourMusic = false;
    $addedMsg = '';
    $removedMsg = '';
    $updatedMsg = '';
    $yourMusicMsg = '';
    if ($nb_added_playlists > 0) {
        $addedMsg = $nb_added_playlists.' added';
        $changedPlaylists = true;
    }

    if ($nb_removed_playlists > 0) {
        $removedMsg = $nb_removed_playlists.' removed';
        $changedPlaylists = true;
    }

    if ($nb_updated_playlists > 0) {
        $updatedMsg = $nb_updated_playlists.' updated';
        $changedPlaylists = true;
    }

    if ($your_music_updated) {
        $yourMusicMsg = ' - Your Music: updated';
        $changedYourMusic = true;
    }

    if ($changedPlaylists && $changedYourMusic) {
        $message = 'Playlists: '.$addedMsg.' '.$removedMsg.' '.$updatedMsg.' '.$yourMusicMsg;
    } elseif ($changedPlaylists) {
        $message = 'Playlists: '.$addedMsg.' '.$removedMsg.' '.$updatedMsg;
    } elseif ($changedYourMusic) {
        $message = $yourMusicMsg;
    } else {
        $message = 'No change';
    }

    if(getenv('reduce_notifications') == 0) {
        displayNotificationWithArtwork($w, $message.' - took '.beautifyTime($elapsed_time, true), './images/update.png', 'Library refreshed');
    }

    if (file_exists($w->data().'/library_old.db')) {
        deleteTheFile($w->data().'/library_old.db');
    }
    rename($w->data().'/library_new.db', $w->data().'/library.db');

    if ($use_artworks) {
        // Download artworks in background
        logMsg('========DOWNLOAD_ARTWORKS DURING REFRESH LIBRARY ========');
        exec('php -f ./src/action.php -- "" "DOWNLOAD_ARTWORKS" "DOWNLOAD_ARTWORKS" >> "'.$w->cache().'/action.log" 2>&1 & ');
    }

    deleteTheFile($w->data().'/update_library_in_progress');
}

/**
 * handleDbIssuePdoXml function.
 *
 * @param mixed $dbhandle
 */
function handleDbIssuePdoXml($dbhandle)
{
    $errorInfo = $dbhandle->errorInfo();
    $w = new Workflows('com.vdesabou.spotify.mini.player');
    $w->result(null, '', 'Database Error: '.$errorInfo[0].' '.$errorInfo[1].' '.$errorInfo[2], '', './images/warning.png', 'no', null, '');
    $w->result(null, '', 'There is a problem with the library, try to re-create it.', 'Select Re-Create Library library below', './images/warning.png', 'no', null, '');
    $w->result(null, serialize(array(
                '' /*track_uri*/,
                '' /* album_uri */,
                '' /* artist_uri */,
                '' /* playlist_uri */,
                '' /* spotify_command */,
                '' /* query */,
                '' /* other_settings*/,
                'update_library' /* other_action */,
                '' /* alfred_playlist_uri */,
                '' /* artist_name */,
                '' /* track_name */,
                '' /* album_name */,
                '' /* track_artwork_path */,
                '' /* artist_artwork_path */,
                '' /* album_artwork_path */,
                '' /* playlist_name */,
                '' /* playlist_artwork_path */,
                '',
                /* $alfred_playlist_name */
            )), 'Re-Create Library', "when done you'll receive a notification. you can check progress by invoking the workflow again", './images/update.png', 'yes', null, '');
    echo $w->tojson();
}

/**
 * handleDbIssuePdoEcho function.
 *
 * @param mixed $dbhandle
 * @param mixed $w
 */
function handleDbIssuePdoEcho($dbhandle, $w)
{
    $errorInfo = $dbhandle->errorInfo();
    logMsg('DB Exception: '.$errorInfo[0].' '.$errorInfo[1].' '.$errorInfo[2]);

    if (file_exists($w->data().'/update_library_in_progress')) {
        deleteTheFile($w->data().'/update_library_in_progress');
    }

    // set back old library
    if (file_exists($w->data().'/library_new.db')) {
        rename($w->data().'/library_new.db', $w->data().'/library.db');
    }

    if (file_exists($w->data().'/library_old.db')) {
        deleteTheFile($w->data().'/library_old.db');
    }

    displayNotificationWithArtwork($w, 'DB Exception: '.$errorInfo[2], './images/warning.png');

    exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' DB Exception: '.escapeQuery($errorInfo[2])."\"'");

    exit;
}

/**
 * handleSpotifyWebAPIException function.
 *
 * @param mixed $w
 * @param mixed $e
 */
function handleSpotifyWebAPIException($w, $e)
{
    if (file_exists($w->data().'/update_library_in_progress')) {
        deleteTheFile($w->data().'/update_library_in_progress');
    }

    // remove the new library (it failed)
    if (file_exists($w->data().'/library_new.db')) {
        deleteTheFile($w->data().'/library_new.db');
    }

    // set back old library
    if (file_exists($w->data().'/library_old.db')) {
        rename($w->data().'/library_old.db', $w->data().'/library.db');
    }

    displayNotificationWithArtwork($w, 'Web API Exception: '.$e->getCode().' - '.$e->getMessage().' use spot_mini_debug command', './images/warning.png', 'Error!');

    exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini_debug').' Web API Exception: '.escapeQuery($e->getMessage())."\"'");

    exit;
}

/**
 * floatToSquares function.
 *
 * @param mixed $decimal
 */
function floatToSquares($decimal)
{
    $squares = ($decimal < 1) ? floor($decimal * 10) : 10;

    return str_repeat('◼︎', $squares).str_repeat('◻︎', 10 - $squares);
}

/**
 * floatToStars function.
 *
 * @param mixed $decimal
 */
function floatToStars($decimal)
{
    if ($decimal == 0) {
        return '';
    }
    $squares = ($decimal < 1) ? floor($decimal * 5) : 5;

    return str_repeat('★', $squares).str_repeat('☆', 5 - $squares);
}

/**
 * Mulit-byte Unserialize.
 *
 * UTF-8 will screw up a serialized string
 * Thanks to http://stackoverflow.com/questions/2853454/php-unserialize-fails-with-non-encoded-characters
 *
 * @param string
 *
 * @return string
 */
function mb_unserialize($string)
{
    $string2 = preg_replace_callback(
        '!s:(\d+):"(.*?)";!s',
        function ($m) {
            $len = strlen($m[2]);
            $result = "s:$len:\"{$m[2]}\";";

            return $result;
        },
        $string);

    return unserialize($string2);
}

/**
 * cleanupTrackName function.
 *
 * @param mixed $track_name
 */
function cleanupTrackName($track_name)
{
    return str_ireplace(array(
            'acoustic version',
            'new album version',
            'original album version',
            'album version',
            'bonus track',
            'clean version',
            'club mix',
            'demo version',
            'extended mix',
            'extended outro',
            'extended version',
            'extended',
            'explicit version',
            'explicit',
            '(live)',
            '- live',
            'live version',
            'lp mix',
            '(original)',
            'original edit',
            'original mix edit',
            'original version',
            '(radio)',
            'radio edit',
            'remix edit',
            'radio mix',
            'remastered version',
            're-mastered version',
            'remastered digital version',
            're-mastered digital version',
            'remastered',
            'remaster',
            'remixed version',
            'remix',
            'single version',
            'studio version',
            'version acustica',
            'versión acústica',
            'vocal edit',
        ), '', $track_name);
}

/**
 * cleanupArtistName function.
 *
 * @param mixed $artist_name
 */
function cleanupArtistName($artist_name)
{
    $query_artist = $artist_name;
    if (stristr($query_artist, 'feat.')) {
        $query_artist = stristr($query_artist, 'feat.', true);
    } elseif (stristr($query_artist, 'featuring')) {
        $query_artist = stristr($query_artist, 'featuring', true);
    } elseif (stristr($query_artist, ' & ')) {
        $query_artist = stristr($query_artist, ' & ', true);
    }

    $query_artist = str_replace('&', 'and', $query_artist);
    $query_artist = str_replace('$', 's', $query_artist);
    $query_artist = strip_string(trim($query_artist));
    $query_artist = str_replace(' - ', '-', $query_artist);
    $query_artist = str_replace(' ', '-', $query_artist);

    return $query_artist;
}

/*

This function was mostly taken from SpotCommander.

SpotCommander is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

SpotCommander is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SpotCommander.  If not, see <http://www.gnu.org/licenses/>.

Copyright 2013 Ole Jon Bjørkum

* getLyrics function.
*
* @access public
* @param mixed $w
* @param mixed $artist
* @param mixed $title
* @return lyrics
*/
function getLyrics($w, $artist, $title)
{
    $query_artist = $artist;
    $query_title = $title;

    $query_artist = cleanupArtistName($query_artist);
    $query_title = cleanupTrackName($query_title);

    if (stristr($query_title, 'feat.')) {
        $query_title = stristr($query_title, 'feat.', true);
    } elseif (stristr($query_title, 'featuring')) {
        $query_title = stristr($query_title, 'featuring', true);
    } elseif (stristr($query_title, ' con ')) {
        $query_title = stristr($query_title, ' con ', true);
    } elseif (stristr($query_title, '(includes')) {
        $query_title = stristr($query_title, '(includes', true);
    } elseif (stristr($query_title, '(live at')) {
        $query_title = stristr($query_title, '(live at', true);
    } elseif (stristr($query_title, 'revised')) {
        $query_title = stristr($query_title, 'revised', true);
    } elseif (stristr($query_title, '(19')) {
        $query_title = stristr($query_title, '(19', true);
    } elseif (stristr($query_title, '(20')) {
        $query_title = stristr($query_title, '(20', true);
    } elseif (stristr($query_title, '- 19')) {
        $query_title = stristr($query_title, '- 19', true);
    } elseif (stristr($query_title, '- 20')) {
        $query_title = stristr($query_title, '- 20', true);
    }

    $query_title = str_replace('&', 'and', $query_title);
    $query_title = str_replace('$', 's', $query_title);
    $query_title = strip_string(trim($query_title));
    $query_title = str_replace(' - ', '-', $query_title);
    $query_title = str_replace(' ', '-', $query_title);
    $query_title = rtrim($query_title, '-');

    $uri = strtolower('https://www.musixmatch.com/lyrics/'.$query_artist.'/'.$query_title);
    $error = false;
    $no_match = false;

    $options = array(
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
    );
    $file = $w->request($uri, $options);

    preg_match('/<script>.*var __mxmState = (.*?);<\/script>/s', $file, $lyrics);
    $lyrics = (empty($lyrics[1])) ? '' : $lyrics[1];
    if (empty($file)) {
        return array(
            false,
            '',
        );
    } elseif ($lyrics == '') {
        $no_match = true;

        return array(
            false,
            '',
        );
    } else {
        $json = json_decode($lyrics);
        switch (json_last_error()) {
        case JSON_ERROR_DEPTH:
            return array(
                false,
                '',
            );
        case JSON_ERROR_CTRL_CHAR:
            return array(
                false,
                '',
            );
        case JSON_ERROR_SYNTAX:
            return array(
                false,
                '',
            );
        case JSON_ERROR_NONE:

            if (isset($json->page) &&
                isset($json->page->lyrics) &&
                isset($json->page->lyrics->lyrics)) {
                if ($json->page->lyrics->lyrics->body == '') {
                    return array(
                        false,
                        '',
                    );
                } else {
                    return array(
                        $uri,
                        $json->page->lyrics->lyrics->body,
                    );
                }
            } else {
                return array(
                    false,
                    '',
                );
            }
        }
    }
}

/**
 * strip_string function.
 *
 * @param mixed $string
 */
function strip_string($string)
{
    return preg_replace('/[^a-zA-Z0-9-\s]/', '', $string);
}

/**
 * checkForUpdate function.
 *
 * @param mixed $w
 * @param mixed $last_check_update_time
 * @param bool  $download               (default: true)
 */
function checkForUpdate($w, $last_check_update_time, $download = false)
{
    if (time() - $last_check_update_time > 604800 || $download == true) {
        // update last_check_update_time
        $ret = updateSetting($w, 'last_check_update_time', time());
        if ($ret == false) {
            return 'Error while updating settings';
        }

        if (!$w->internet()) {
            return 'No internet connection !';
        }

        // get local information
        if (!file_exists('./packal/package.xml')) {
            return 'This release has not been downloaded from Packal';
        }
        $xml = $w->read('./packal/package.xml');
        $workflow = new SimpleXMLElement($xml);
        $local_version = $workflow->version;
        $remote_json = 'https://raw.githubusercontent.com/vdesabou/alfred-spotify-mini-player/master/remote.json';

        // get remote information
        $jsonDataRemote = $w->request($remote_json);

        if (empty($jsonDataRemote)) {
            return 'The export.json '.$remote_json.' file cannot be found';
        }

        $json = json_decode($jsonDataRemote, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $download_url = $json['download_url'];
            $remote_version = $json['version'];
            $description = $json['description'];

            if ($local_version < $remote_version) {
                if ($download == true) {
                    $workflow_file_name = exec('printf $HOME').'/Downloads/spotify-mini-player-'.$remote_version.'.alfredworkflow';
                    $fp = fopen($workflow_file_name, 'w+');
                    $options = array(
                        CURLOPT_FILE => $fp,
                    );
                    $w->request("$download_url", $options);

                    return array(
                        $remote_version,
                        $workflow_file_name,
                        $description,
                    );
                } else {
                    $w->result(null, serialize(array(
                                '' /*track_uri*/,
                                '' /* album_uri */,
                                '' /* artist_uri */,
                                '' /* playlist_uri */,
                                '' /* spotify_command */,
                                '' /* query */,
                                '' /* other_settings*/,
                                'download_update' /* other_action */,
                                '' /* artist_name */,
                                '' /* track_name */,
                                '' /* album_name */,
                                '' /* track_artwork_path */,
                                '' /* artist_artwork_path */,
                                '' /* album_artwork_path */,
                                '' /* playlist_name */,
                                '', /* playlist_artwork_path */
                            )), 'An update is available, version '.$remote_version.'. Click to download', ''.$description, './images/check_update.png', 'yes', '');
                }
            }
        } else {
            return 'Cannot read remote.json';
        }
    }
}

/**
 * doJsonRequest function.
 *
 * @param mixed $w
 * @param mixed $url
 * @param bool  $actionMode (default: true)
 */
function doJsonRequest($w, $url, $actionMode = true)
{
    if (!$w->internet()) {
        if ($actionMode == true) {
            displayNotificationWithArtwork($w, 'No internet connection', './images/warning.png');

            exit;
        } else {
            $w->result(null, '', 'Error: No internet connection', $url, './images/warning.png', 'no', null, '');
            echo $w->tojson();
            exit;
        }
    }

    $json = $w->request($url);
    if (empty($json)) {
        if ($actionMode == true) {
            displayNotificationWithArtwork($w, 'Error: JSON request returned empty result', './images/warning.png');

            exit;
        } else {
            $w->result(null, '', 'Error: JSON request returned empty result', $url, './images/warning.png', 'no', null, '');
            echo $w->tojson();
            exit;
        }
    }

    $json = json_decode($json);
    switch (json_last_error()) {
    case JSON_ERROR_NONE:
        return $json;
    default:
        if ($actionMode == true) {
            displayNotificationWithArtwork($w, 'Error: JSON request returned error '.json_last_error().' ('.json_last_error_msg().')', './images/warning.png');

            exit;
        } else {
            $w->result(null, '', 'Error: JSON request returned error '.json_last_error().' ('.json_last_error_msg().')', 'Try again or report to author', './images/warning.png', 'no', null, '');
            echo $w->tojson();
            exit;
        }
    }
}

/**
 * killUpdate function.
 *
 * @param mixed $w
 */
function killUpdate($w)
{
    deleteTheFile($w->data().'/update_library_in_progress');
    deleteTheFile($w->data().'/download_artworks_in_progress');

    if (file_exists($w->data().'/library_old.db')) {
        rename($w->data().'/library_old.db', $w->data().'/library.db');
    }

    if (file_exists($w->data().'/library_new.db')) {
        deleteTheFile($w->data().'/library_new.db');
    }

    exec("kill -9 $(ps -efx | grep \"php\" | egrep \"update_|php -S localhost:15298|ADDTOPLAYLIST|UPDATE_|DOWNLOAD_ARTWORKS\" | grep -v grep | awk '{print $2}')");

    displayNotificationWithArtwork($w, 'Update library was killed', './images/kill.png', 'Kill Update Library ');
}

/**
 * deleteTheFile function.
 *
 * @param mixed $filename
 */
function deleteTheFile($filename)
{
    @chmod($filename, 0777);
    @unlink(realpath($filename));

    if (is_file($filename)) {
        logMsg('Error(deleteTheFile): file was locked (or permissions error) '.realpath($filename).' permissions: '.decoct(fileperms(realpath($filename)) & 0777));
        displayNotificationWithArtwork($w, 'Problem deleting '.$filename, './images/warning.png', 'Delete File');
    }
}

/**
 * getCountryName function.
 *
 * @param mixed $cc
 */
function getCountryName($cc)
{
    // from http://stackoverflow.com/questions/14599400/how-to-get-iso-3166-1-compatible-country-code
    $country_names = json_decode(file_get_contents('./src/country_names.json'), true);

    return $country_names[$cc];
}

/**
 * beautifyTime function.
 *
 * @param mixed $seconds
 * @param bool  $withText (default: false)
 */
function beautifyTime($seconds, $withText = false)
{
    $ret = gmdate('H●i●s', $seconds);
    $tmp = explode('●', $ret);
    if ($tmp[0] == '00' && $tmp[1] != '00') {
        $min = ltrim($tmp[1], 0);

        if ($withText == true) {
            return "$min min $tmp[2] sec";
        } else {
            return "$min:$tmp[2]";
        }
    } elseif ($tmp[1] == '00') {
        $sec = ltrim($tmp[2], 0);
        if ($sec == '') {
            $sec = 0;
        }

        if ($withText == true) {
            return "$sec sec";
        } else {
            return "0:$tmp[2]";
        }
    } else {
        $hr = ltrim($tmp[0], 0);
        $min = ltrim($tmp[1], 0);

        return "$hr hr $min min";
    }
}

/**
 * startswith function.
 *
 * @param mixed $haystack
 * @param mixed $needle
 */
function startswith($haystack, $needle)
{
    return substr($haystack, 0, strlen($needle)) === $needle;
}


/**
 * getSettings function.
 *
 * @param mixed $w
 */
function getSettings($w)
{
    $settings = $w->read('settings.json');

    if ($settings == false) {
        $default = array(
            'all_playlists' => 1,
            'is_alfred_playlist_active' => 1,
            'radio_number_tracks' => 30,
            'now_playing_notifications' => 1,
            'max_results' => 50,
            'alfred_playlist_uri' => '',
            'alfred_playlist_name' => '',
            'country_code' => '',
            'last_check_update_time' => 0,
            'oauth_client_id' => '',
            'oauth_client_secret' => '',
            'oauth_redirect_uri' => 'http://localhost:15298/callback.php',
            'oauth_access_token' => '',
            'oauth_expires' => 0,
            'oauth_refresh_token' => '',
            'display_name' => '',
            'userid' => '',
            'is_public_playlists' => 0,
            'quick_mode' => 0,
            'output_application' => 'APPLESCRIPT',
            'mopidy_server' => '127.0.0.1',
            'mopidy_port' => '6680',
            'volume_percent' => 20,
            'is_display_rating' => 1,
            'is_autoplay_playlist' => 1,
            'use_growl' => 0,
            'use_facebook' => 0,
            'theme_color' => 'green',
            'search_order' => 'playlist▹artist▹track▹album',
            'always_display_lyrics_in_browser' => 0,
        );

        $ret = $w->write($default, 'settings.json');
        displayNotificationWithArtwork($w, 'Settings have been set to default', './images/info.png', 'Settings reset');

        $settings = $w->read('settings.json');
    }

    // add quick_mode if needed
    if (!isset($settings->quick_mode)) {
        updateSetting($w, 'quick_mode', 0);
        $settings = $w->read('settings.json');
    }

    // add mopidy_server if needed
    if (!isset($settings->mopidy_server)) {
        updateSetting($w, 'mopidy_server', '127.0.0.1');
        $settings = $w->read('settings.json');
    }

    // add mopidy_port if needed
    if (!isset($settings->mopidy_port)) {
        updateSetting($w, 'mopidy_port', '6680');
        $settings = $w->read('settings.json');
    }

    // add volume_percent if needed
    if (!isset($settings->volume_percent)) {
        updateSetting($w, 'volume_percent', 20);
        $settings = $w->read('settings.json');
    }

    // add is_display_rating if needed
    if (!isset($settings->is_display_rating)) {
        updateSetting($w, 'is_display_rating', 1);
        $settings = $w->read('settings.json');
    }

    // add is_autoplay_playlist if needed
    if (!isset($settings->is_autoplay_playlist)) {
        updateSetting($w, 'is_autoplay_playlist', 1);
        $settings = $w->read('settings.json');
    }

    // add use_growl if needed
    if (!isset($settings->use_growl)) {
        updateSetting($w, 'use_growl', 0);
        $settings = $w->read('settings.json');
    }

    // add use_artworks if needed
    if (!isset($settings->use_artworks)) {
        updateSetting($w, 'use_artworks', 1);
        $settings = $w->read('settings.json');
    }

    // add use_facebook if needed
    if (!isset($settings->use_facebook)) {
        updateSetting($w, 'use_facebook', 0);
        $settings = $w->read('settings.json');
    }

    // add theme_color if needed
    if (!isset($settings->theme_color)) {
        updateSetting($w, 'theme_color', 'green');
        $settings = $w->read('settings.json');
    }

    // add search_order if needed
    if (!isset($settings->search_order)) {
        updateSetting($w, 'search_order', 'playlist▹artist▹track▹album');
        $settings = $w->read('settings.json');
    }

    // add always_display_lyrics_in_browser if needed
    if (!isset($settings->always_display_lyrics_in_browser)) {
        updateSetting($w, 'always_display_lyrics_in_browser', 0);
        $settings = $w->read('settings.json');
    }

    // migrate use_mopidy
    if (isset($settings->use_mopidy)) {
        if ($settings->use_mopidy) {
            updateSetting($w, 'output_application', 'MOPIDY');
        } else {
            updateSetting($w, 'output_application', 'APPLESCRIPT');
        }
        removeSetting($w,'use_mopidy');
        $settings = $w->read('settings.json');
    }
    
    return $settings;
}

/**
 * updateSetting function.
 *
 * @param mixed  $w
 * @param mixed  $setting_name
 * @param mixed  $setting_new_value
 * @param string $settings_file     (default: 'settings.json')
 */
function updateSetting($w, $setting_name, $setting_new_value, $settings_file = 'settings.json')
{
    $settings = $w->read($settings_file);

    if ($settings == false) {
        logMsg('Error: updateSetting failed while reading JSON file');

        return false;
    }
    $new_settings = array();
    $found = false;

    foreach ($settings as $key => $value) {
        if ($key == $setting_name) {
            $new_settings[$key] = $setting_new_value;
            $found = true;
        } else {
            $new_settings[$key] = $value;
        }
    }
    if ($found == false) {
        $new_settings[$setting_name] = $setting_new_value;
    }
    $ret = $w->write($new_settings, $settings_file);

    return $ret;
}

/**
 * removeSetting function.
 *
 * @param mixed  $w
 * @param mixed  $setting_name
 * @param string $settings_file     (default: 'settings.json')
 */
 function removeSetting($w, $setting_name, $settings_file = 'settings.json')
 {
     $settings = $w->read($settings_file);
 
     if ($settings == false) {
         logMsg('Error: removeSetting failed while reading JSON file');
 
         return false;
     }
     $new_settings = array();
 
     foreach ($settings as $key => $value) {
         if ($key == $setting_name) {
             // do nothing
         } else {
             $new_settings[$key] = $value;
         }
     }
     $ret = $w->write($new_settings, $settings_file);
 
     return $ret;
 }

/**
 * logMsg function.
 *
 * @param mixed $w
 * @param mixed $msg
 */
function logMsg($msg)
{
    date_default_timezone_set('UTC');
    $date = date('Y-m-d H:i:s', time());
    file_put_contents('php://stderr', "$date"."|{$msg}".PHP_EOL);
}

/**
 * copyDirectory function.
 *
 * @param mixed $source
 * @param mixed $destination
 */
function copyDirectory($source, $destination)
{
    if (is_dir($source)) {
        @mkdir($destination);
        $directory = dir($source);
        while (false !== ($readdirectory = $directory->read())) {
            if ($readdirectory == '.' || $readdirectory == '..') {
                continue;
            }
            $PathDir = $source.'/'.$readdirectory;
            if (is_dir($PathDir)) {
                copyDirectory($PathDir, $destination.'/'.$readdirectory);
                continue;
            }
            copy($PathDir, $destination.'/'.$readdirectory);
        }

        $directory->close();
    } else {
        copy($source, $destination);
    }
}

/**
 * removeDirectory function.
 *
 * @param mixed $path
 */
function removeDirectory($path)
{
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            removeDirectory(realpath($path).'/'.$file);
        }

        return rmdir($path);
    } elseif (is_file($path) === true) {
        return unlink($path);
    }

    return false;
}

///////////////

// StatHat integration

/**
 * do_post_request function.
 *
 * @param mixed $url
 * @param mixed $data
 * @param mixed $optional_headers (default: null)
 */
function do_post_request($url, $data, $optional_headers = null)
{
    $params = array(
        'http' => array(
            'method' => 'POST',
            'content' => $data,
        ),
    );
    if ($optional_headers !== null) {
        $params['http']['header'] = $optional_headers;
    }
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if (!$fp) {
        throw new Exception("Problem with $url, $php_errormsg");
    }
    $response = @stream_get_contents($fp);
    if ($response === false) {
        throw new Exception("Problem reading data from $url, $php_errormsg");
    }

    return $response;
}

/**
 * do_async_post_request function.
 *
 * @param mixed $url
 * @param mixed $params
 */
function do_async_post_request($url, $params)
{
    foreach ($params as $key => &$val) {
        if (is_array($val)) {
            $val = implode(',', $val);
        }
        $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $parts = parse_url($url);

    $fp = @fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);

    if ($fp) {
        $out = 'POST '.$parts['path']." HTTP/1.1\r\n";
        $out .= 'Host: '.$parts['host']."\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= 'Content-Length: '.strlen($post_string)."\r\n";
        $out .= "Connection: Close\r\n\r\n";
        if (isset($post_string)) {
            $out .= $post_string;
        }

        fwrite($fp, $out);
        fclose($fp);
    } else {
        logMsg('Error: Problem when updating stat with stathat');
    }
}

/**
 * stathat_count function.
 *
 * @param mixed $stat_key
 * @param mixed $user_key
 * @param mixed $count
 */
function stathat_count($stat_key, $user_key, $count)
{
    return do_async_post_request('http://api.stathat.com/c', array(
            'key' => $stat_key,
            'ukey' => $user_key,
            'count' => $count,
        ));
}

/**
 * stathat_value function.
 *
 * @param mixed $stat_key
 * @param mixed $user_key
 * @param mixed $value
 */
function stathat_value($stat_key, $user_key, $value)
{
    do_async_post_request('http://api.stathat.com/v', array(
            'key' => $stat_key,
            'ukey' => $user_key,
            'value' => $value,
        ));
}

/**
 * stathat_ez_count function.
 *
 * @param mixed $email
 * @param mixed $stat_name
 * @param mixed $count
 */
function stathat_ez_count($email, $stat_name, $count)
{
    do_async_post_request('http://api.stathat.com/ez', array(
            'email' => $email,
            'stat' => $stat_name,
            'count' => $count,
        ));
}

/**
 * stathat_ez_value function.
 *
 * @param mixed $email
 * @param mixed $stat_name
 * @param mixed $value
 */
function stathat_ez_value($email, $stat_name, $value)
{
    do_async_post_request('http://api.stathat.com/ez', array(
            'email' => $email,
            'stat' => $stat_name,
            'value' => $value,
        ));
}

/**
 * stathat_count_sync function.
 *
 * @param mixed $stat_key
 * @param mixed $user_key
 * @param mixed $count
 */
function stathat_count_sync($stat_key, $user_key, $count)
{
    return do_post_request('http://api.stathat.com/c', "key=$stat_key&ukey=$user_key&count=$count");
}

/**
 * stathat_value_sync function.
 *
 * @param mixed $stat_key
 * @param mixed $user_key
 * @param mixed $value
 */
function stathat_value_sync($stat_key, $user_key, $value)
{
    return do_post_request('http://api.stathat.com/v', "key=$stat_key&ukey=$user_key&value=$value");
}

/**
 * stathat_ez_count_sync function.
 *
 * @param mixed $email
 * @param mixed $stat_name
 * @param mixed $count
 */
function stathat_ez_count_sync($email, $stat_name, $count)
{
    return do_post_request('http://api.stathat.com/ez', "email=$email&stat=$stat_name&count=$count");
}

/**
 * stathat_ez_value_sync function.
 *
 * @param mixed $email
 * @param mixed $stat_name
 * @param mixed $value
 */
function stathat_ez_value_sync($email, $stat_name, $value)
{
    return do_post_request('http://api.stathat.com/ez', "email=$email&stat=$stat_name&value=$value");
}

/**
* Thanks to http://stackoverflow.com/questions/2690504/php-producing-relative-date-time-from-timestamps
*/
function time2str($ts)
{
    if(!ctype_digit($ts))
        $ts = strtotime($ts);

    $diff = time() - $ts;
    if($diff == 0)
        return 'now';
    elseif($diff > 0)
    {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 60) return 'just now';
            if($diff < 120) return '1 minute ago';
            if($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if($diff < 7200) return '1 hour ago';
            if($diff < 86400) return floor($diff / 3600) . ' hours ago';
        }
        if($day_diff == 1) return 'Yesterday';
        if($day_diff < 7) return $day_diff . ' days ago';
        if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
        if($day_diff < 60) return 'last month';
        return date('F Y', $ts);
    }
    else
    {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 120) return 'in a minute';
            if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
            if($diff < 7200) return 'in an hour';
            if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
        }
        if($day_diff == 1) return 'Tomorrow';
        if($day_diff < 4) return date('l', $ts);
        if($day_diff < 7 + (7 - date('w'))) return 'next week';
        if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
        if(date('n', $ts) == date('n') + 1) return 'next month';
        return date('F Y', $ts);
    }
}
