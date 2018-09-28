<?php

require './src/functions.php';
require_once './src/workflows.php';
$w = new Workflows('com.vdesabou.spotify.mini.player');

// Report all PHP errors
error_reporting(E_ALL);

$query = $argv[1];
$type = $argv[2];
$add_to_option = $argv[3];

$arg = mb_unserialize($query);
$track_uri = $arg[0];
$album_uri = $arg[1];
$artist_uri = $arg[2];
$playlist_uri = $arg[3];
$spotify_command = $arg[4];
$original_query = $arg[5];
$other_settings = $arg[6];
$other_action = $arg[7];
$artist_name = $arg[8];
$track_name = $arg[9];
$album_name = $arg[10];
$track_artwork_path = $arg[11];
$artist_artwork_path = $arg[12];
$album_artwork_path = $arg[13];
$playlist_name = $arg[14];
$playlist_artwork_path = $arg[15];

// Read settings from JSON

$settings = getSettings($w);
$is_alfred_playlist_active = $settings->is_alfred_playlist_active;
$now_playing_notifications = $settings->now_playing_notifications;
$alfred_playlist_uri = $settings->alfred_playlist_uri;
$alfred_playlist_name = $settings->alfred_playlist_name;
$country_code = $settings->country_code;
$userid = $settings->userid;
$oauth_client_id = $settings->oauth_client_id;
$oauth_client_secret = $settings->oauth_client_secret;
$oauth_redirect_uri = $settings->oauth_redirect_uri;
$oauth_access_token = $settings->oauth_access_token;
$volume_percent = $settings->volume_percent;
$use_artworks = $settings->use_artworks;
$use_facebook = $settings->use_facebook;
$always_display_lyrics_in_browser = $settings->always_display_lyrics_in_browser;
$output_application = $settings->output_application;


if ($other_action != 'reset_settings' && $other_action != 'spot_mini_debug' && !startswith($other_settings,'SWITCH_USER▹')) {
    if ($oauth_client_id == '' || $oauth_client_secret == '' || $oauth_access_token == '') {
        if ($other_settings != '' && (startsWith($other_settings, 'Oauth_Client') === false && startsWith($other_settings, 'Open') === false)) {
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini')." \"'");

            return;
        }

        if ($other_action != '' && $other_action != 'Oauth_Login' &&
            !startsWith($other_action, 'current')) {
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini')." \"'");

            return;
        }
    }
}

if ($userid != 'vdesabou' && !startsWith($other_action, 'current')) {
    stathat_ez_count('AlfredSpotifyMiniPlayer', 'workflow used', 1);
}

if ($add_to_option != '') {
    if (file_exists($w->data().'/update_library_in_progress')) {
        displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

        return;
    }
}

// start now playing if needed
if($oauth_access_token != '') {
    $app_arg = '';
    if ($output_application == 'MOPIDY') {
        $app_arg = 'MOPIDY';
    } else if($output_application == 'APPLESCRIPT') {
        $app_arg = 'SPOTIFY';
    } else {
        $app_arg = 'CONNECT';
    }
    exec('./src/spotify_mini_player_notifications.ksh -d "'.$w->data().'" -a start -m "'.$app_arg.'"  >> "'.$w->cache().'/action.log" 2>&1 & ');
}

// make sure spotify is running
if ($output_application == 'APPLESCRIPT') {
    if($oauth_access_token != '' && $other_action != 'update_library' && $other_action != 'refresh_library' && $type != 'DOWNLOAD_ARTWORKS') {
        exec('./src/is_spotify_running.ksh 2>&1', $retArr, $retVal);
        if ($retArr[0] != 0) {
            exec('open -a "Spotify"');
            // wait for Spotify to start
            sleep(6);
        }
    }
}

if ($spotify_command != '' && $type == 'TRACK' && $add_to_option == '') {
    $spotify_command = str_replace('\\', '', $spotify_command);
    if (!startsWith($spotify_command, 'activate')) {
        exec("osascript -e 'tell application \"Spotify\" to activate'");
        exec("osascript -e 'set uri to \"spotify:search:\" & \"$spotify_command\"' -e 'tell application \"Finder\" to open location uri'");
    } else {
        exec("osascript -e 'tell application \"Spotify\" to $spotify_command'");
    }
    return;
}

if ($type == 'TRACK' && $other_settings == '' &&
    (startsWith($other_action, 'set_playlist_privacy_to_') || $other_action == 'play_track_from_play_queue' || $other_action == ''
        || ($other_action == 'play_track_in_album_context' && $add_to_option != '')
        || ($other_action == 'play' && $add_to_option != '')
        || ($other_action == 'playpause' && $add_to_option != '')
        || ($other_action == 'pause' && $add_to_option != ''))) {
    if ($track_uri != '') {
        if ($add_to_option != '') {
            $tmp = explode(':', $track_uri);
            if ($tmp[1] == 'local') {
                // local track, look it up online
                $query = 'track:'.strtolower($track_name).' artist:'.strtolower($artist_name);
                $results = searchWebApi($w, $country_code, $query, 'track', 1);

                if (count($results) > 0) {
                    // only one track returned
                    $track = $results[0];
                    $artists = $track->artists;
                    $artist = $artists[0];
                    logMsg("Unknown track $track_uri / $track_name / $artist_name replaced by track: $track->uri / $track->name / $artist->name");
                    $track_uri = $track->uri;
                    $tmp = explode(':', $track_uri);
                } else {
                    logMsg("Could not find track: $track_uri / $track_name / $artist_name");
                    displayNotificationWithArtwork($w, 'Local track '.$track_name.' has not online match', './images/warning.png', 'Error!');

                    return;
                }
            }
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Add▹'.$track_uri.'∙'.escapeQuery($track_name).'▹'."\"'");

            return;
        } elseif ($playlist_uri != '') {
            if ($output_application == 'MOPIDY') {
                playTrackInContextWithMopidy($w, $track_uri, $playlist_uri);
            } else if($output_application == 'APPLESCRIPT') {
                exec("osascript -e 'tell application \"Spotify\" to play track \"$track_uri\" in context \"$playlist_uri\"'");
            } else {
                $device_id = getSpotifyConnectCurrentDeviceId($w);
                if($device_id != '') {
                    playTrackSpotifyConnect($w, $device_id, $track_uri, $playlist_uri);
                } else {
                    displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
                }
            }

            if ($now_playing_notifications == false) {
                displayNotificationWithArtwork($w, '🔈 '.$track_name.' by '.$artist_name, $track_artwork_path);
            }
            if ($userid != 'vdesabou') {
                stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
            }
            addPlaylistToPlayQueue($w, $playlist_uri, $playlist_name);

            return;
        } else {
            if ($other_action == '' || $other_action == 'play_track_from_play_queue') {
                if ($output_application == 'MOPIDY') {
                    playUriWithMopidyWithoutClearing($w, $track_uri);
                } else if($output_application == 'APPLESCRIPT') {
                    exec("osascript -e 'tell application \"Spotify\" to play track \"$track_uri\"'");
                } else {
                    $device_id = getSpotifyConnectCurrentDeviceId($w);
                    if($device_id != '') {
                        playTrackSpotifyConnect($w, $device_id, $track_uri, '');
                    } else {
                        displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
                    }
                }
                if ($now_playing_notifications == false) {
                    displayNotificationWithArtwork($w, '🔈 '.$track_name.' by '.$artist_name, $track_artwork_path);
                }
                if ($userid != 'vdesabou') {
                    stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
                }
                if ($other_action == '') {
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
                        addTrackToPlayQueue($w, $track_uri, escapeQuery($results[0]), escapeQuery($results[1]), escapeQuery($results[2]), $results[5], $country_code);
                    }
                }

                return;
            }
        }
    } elseif ($playlist_uri != '') {
        if ($output_application == 'MOPIDY') {
            playUriWithMopidy($w, $playlist_uri);
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to play track \"$playlist_uri\"'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                playTrackSpotifyConnect($w, $device_id, '', $playlist_uri);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        if ($playlist_artwork_path == '') {
            $playlist_artwork_path = getPlaylistArtwork($w, $playlist_uri, true, false, $use_artworks);
        }
        displayNotificationWithArtwork($w, '🔈 Playlist '.$playlist_name, $playlist_artwork_path, 'Launch Playlist');
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
        }
        addPlaylistToPlayQueue($w, $playlist_uri, $playlist_name);

        return;
    }
} elseif ($type == 'ALBUM') {
    if ($album_uri == '') {
        if ($track_uri == '') {
            displayNotificationWithArtwork($w, 'Cannot get current album', './images/warning.png', 'Error!');

            return;
        }
            // case of current song with alt
            $album_uri = getAlbumUriFromTrack($w, $track_uri);
        if ($album_uri == false) {
            displayNotificationWithArtwork($w, 'Cannot get current album', './images/warning.png', 'Error!');

            return;
        }
        $album_artwork_path = getTrackOrAlbumArtwork($w, $album_uri, true, false, false, $use_artworks);
    }
    if ($output_application == 'MOPIDY') {
        playUriWithMopidy($w, $album_uri);
    } else if($output_application == 'APPLESCRIPT') {
        exec("osascript -e 'tell application \"Spotify\" to play track \"$album_uri\"'");
    } else {
        $device_id = getSpotifyConnectCurrentDeviceId($w);
        if($device_id != '') {
            playTrackSpotifyConnect($w, $device_id, '', $album_uri);
        } else {
            displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
        }
    }
    displayNotificationWithArtwork($w, '🔈 Album '.$album_name.' by '.$artist_name, $album_artwork_path, 'Play Album');
    if ($userid != 'vdesabou') {
        stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
    }
    addAlbumToPlayQueue($w, $album_uri, $album_name);

    return;
} elseif ($type == 'ONLINE') {
    if ($artist_uri == '') {
        // case of current song with cmd
            $artist_uri = getArtistUriFromTrack($w, $track_uri);
        if ($artist_uri == false) {
            displayNotificationWithArtwork($w, 'Cannot get current artist', './images/warning.png', 'Error!');

            return;
        }
    }
    exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Online▹'.$artist_uri.'@'.escapeQuery($artist_name).'▹'."\"'");
    if ($userid != 'vdesabou') {
        stathat_ez_count('AlfredSpotifyMiniPlayer', 'lookup online', 1);
    }

    return;
} elseif ($type == 'ALBUM_OR_PLAYLIST') {
    if ($add_to_option != '') {
        if ($album_name != '') {
            if ($album_uri == '') {
                if ($track_uri == '') {
                    displayNotificationWithArtwork($w, 'Cannot get current album', './images/warning.png', 'Error!');

                    return;
                }
                    // case of current song with shift
                    $album_uri = getAlbumUriFromTrack($w, $track_uri);
                if ($album_uri == false) {
                    displayNotificationWithArtwork($w, 'Cannot get current album', './images/warning.png', 'Error!');

                    return;
                }
                $album_artwork_path = getTrackOrAlbumArtwork($w, $album_uri, true, false, false, $use_artworks);
            }
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Add▹'.$album_uri.'∙'.escapeQuery($album_name).'▹'."\"'");

            return;
        } elseif ($playlist_uri != '') {
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Add▹'.$playlist_uri.'∙'.escapeQuery($playlist_name).'▹'."\"'");

            return;
        }
    }
} elseif ($type == 'DOWNLOAD_ARTWORKS') {
    if ($use_artworks) {
        if (downloadArtworks($w) == false) {
            displayNotificationWithArtwork($w, 'Error when downloading artworks', './images/warning.png', 'Error!');

            return;
        }
    }

    return;
} elseif ($type == 'ARTIST_OR_PLAYLIST_PRIVACY') {
    if ($artist_name != '') {
        if ($artist_uri == '') {
            // case of current song with cmd
                $artist_uri = getArtistUriFromTrack($w, $track_uri);
            if ($artist_uri == false) {
                displayNotificationWithArtwork($w, 'Cannot get current artist', './images/warning.png', 'Error!');

                return;
            }
            $artist_artwork_path = getArtistArtwork($w, $artist_uri, $artist_name, true, false, false, $use_artworks);
        }
        if ($output_application == 'MOPIDY') {
            playUriWithMopidy($w, $artist_uri);
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to play track \"$artist_uri\"'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                playTrackSpotifyConnect($w, $device_id, '', $artist_uri);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        displayNotificationWithArtwork($w, '🔈 Artist '.$artist_name, $artist_artwork_path, 'Play Artist');
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
        }
        addArtistToPlayQueue($w, $artist_uri, $artist_name, $country_code);

        return;
    } elseif ($playlist_uri != '') {
        // case cmd on playlist: change privacy
            // in other_action, the privacy is set
            $tmp = explode(':', $playlist_uri);
        if ($userid != $tmp[2]) {
            displayNotificationWithArtwork($w, 'You cannot update a playlist you don’t own', './images/warning.png', 'Error!');

            return;
        }
        if ($other_action == 'set_playlist_privacy_to_public') {
            $public = true;
            $msgPublic = 'public';
        } elseif ($other_action == 'set_playlist_privacy_to_private') {
            $public = false;
            $msgPublic = 'private';
        } else {
            displayNotificationWithArtwork($w, 'Error when changing playlist privacy', './images/warning.png', 'Error!');

            return;
        }
        setThePlaylistPrivacy($w, $playlist_uri, $playlist_name, $public);
        displayNotificationWithArtwork($w, 'Playlist is now '.$msgPublic, './images/disable_public_playlists.png', 'Change playlist privacy');

        return;
    }
} elseif ($other_settings != '') {
    $setting = explode('▹', $other_settings);
    if ($setting[0] == 'MAX_RESULTS') {
        $ret = updateSetting($w, 'max_results', $setting[1]);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Max results set to '.$setting[1], './images/settings.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($setting[0] == 'RADIO_TRACKS') {
        $ret = updateSetting($w, 'radio_number_tracks', $setting[1]);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Radio track number set to '.$setting[1], './images/settings.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($setting[0] == 'VOLUME_PERCENT') {
        $ret = updateSetting($w, 'volume_percent', $setting[1]);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Volume Percentage set to '.$setting[1], './images/settings.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($setting[0] == 'MOPIDY_SERVER') {
        $ret = updateSetting($w, 'mopidy_server', $setting[1]);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Mopidy server set to '.$setting[1], './images/settings.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($setting[0] == 'MOPIDY_PORT') {
        $ret = updateSetting($w, 'mopidy_port', $setting[1]);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Mopidy TCP port set to '.$setting[1], './images/settings.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($setting[0] == 'Oauth_Client_ID') {
        $ret = updateSetting($w, 'oauth_client_id', $setting[1]);
        if ($ret == true) {
            displayNotificationWithArtwork($w, "Client ID set to $setting[1]", './images/settings.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($setting[0] == 'Oauth_Client_SECRET') {
        $ret = updateSetting($w, 'oauth_client_secret', $setting[1]);
        if ($ret == true) {
            displayNotificationWithArtwork($w, "Client Secret set to $setting[1]", './images/settings.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($setting[0] == 'ALFRED_PLAYLIST') {
        $ret = updateSetting($w, 'alfred_playlist_uri', $setting[1]);
        if ($ret == true) {
            $ret = updateSetting($w, 'alfred_playlist_name', $setting[2]);
            if ($ret == true) {
                displayNotificationWithArtwork($w, 'Alfred Playlist set to '.$setting[2], getPlaylistArtwork($w, $setting[1], true, false, $use_artworks), 'Settings');
            } else {
                displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
            }
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($setting[0] == 'ADD_TO_PLAYLIST') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
            // if playlist_uri is notset, then create it
            if ($setting[1] == 'notset') {
                $new_playlist_uri = createTheUserPlaylist($w, $setting[2]);
                if ($new_playlist_uri != false) {
                    $setting[1] = $new_playlist_uri;
                } else {
                    return;
                }
            }
            // add track to playlist
            if ($track_uri != '') {
                $track_artwork_path = getTrackOrAlbumArtwork($w, $track_uri, true, false, false, $use_artworks);
                $tmp = explode(':', $track_uri);
                if ($tmp[1] == 'local') {
                    // local track, look it up online
                    $query = 'track:'.strtolower($track_name).' artist:'.strtolower($artist_name);
                    $results = searchWebApi($w, $country_code, $query, 'track', 1);

                    if (count($results) > 0) {
                        // only one track returned
                        $track = $results[0];
                        $artists = $track->artists;
                        $artist = $artists[0];
                        logMsg("Unknown track $track_uri / $track_name / $artist_name replaced by track: $track->uri / $track->name / $artist->name");
                        $track_uri = $track->uri;
                        $tmp = explode(':', $track_uri);
                    } else {
                        logMsg("Could not find track: $track_uri / $track_name / $artist_name");
                        displayNotificationWithArtwork($w, 'Local track '.$track_name.' has not online match', './images/warning.png', 'Error!');

                        return;
                    }
                }
                $ret = addTracksToPlaylist($w, $tmp[2], $setting[1], $setting[2], false);
                if ($userid != 'vdesabou') {
                    stathat_ez_count('AlfredSpotifyMiniPlayer', 'add_or_remove', 1);
                }
                if (is_numeric($ret) && $ret > 0) {
                    displayNotificationWithArtwork($w, ''.$track_name.' added to '.$setting[2].' playlist', $track_artwork_path, 'Add Track to Playlist');

                    return;
                } elseif (is_numeric($ret) && $ret == 0) {
                    displayNotificationWithArtwork($w, ''.$track_name.' is already in '.$setting[2].' playlist', './images/warning.png', 'Error!');

                    return;
                }
            } // add playlist to playlist
            elseif ($playlist_uri != '') {
                $playlist_artwork_path = getPlaylistArtwork($w, $playlist_uri, true, true, $use_artworks);
                $ret = addTracksToPlaylist($w, getThePlaylistTracks($w, $playlist_uri), $setting[1], $setting[2], false);
                if ($userid != 'vdesabou') {
                    stathat_ez_count('AlfredSpotifyMiniPlayer', 'add_or_remove', 1);
                }
                if (is_numeric($ret) && $ret > 0) {
                    displayNotificationWithArtwork($w, 'Playlist '.$playlist_name.' added to '.$setting[2].' playlist', $playlist_artwork_path, 'Add Playlist to Playlist');

                    return;
                } elseif (is_numeric($ret) && $ret == 0) {
                    displayNotificationWithArtwork($w, 'Playlist '.$playlist_name.' is already in '.$setting[2].' playlist', './images/warning.png', 'Error!');

                    return;
                }
            } // add album to playlist
            elseif ($album_uri != '') {
                $album_artwork_path = getTrackOrAlbumArtwork($w, $album_uri, true, false, false, $use_artworks);
                $ret = addTracksToPlaylist($w, getTheAlbumTracks($w, $album_uri), $setting[1], $setting[2], false);
                if ($userid != 'vdesabou') {
                    stathat_ez_count('AlfredSpotifyMiniPlayer', 'add_or_remove', 1);
                }
                if (is_numeric($ret) && $ret > 0) {
                    displayNotificationWithArtwork($w, 'Album '.$album_name.' added to '.$setting[2].' playlist', $album_artwork_path, 'Add Album to Playlist');

                    return;
                } elseif (is_numeric($ret) && $ret == 0) {
                    displayNotificationWithArtwork($w, 'Album '.$album_name.' is already in '.$setting[2].' playlist', './images/warning.png', 'Error!');

                    return;
                }
            }
    } elseif ($setting[0] == 'REMOVE_FROM_PLAYLIST') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
            // remove track from playlist
            if ($track_uri != '') {
                $track_artwork_path = getTrackOrAlbumArtwork($w, $track_uri, true, false, false, $use_artworks);
                $tmp = explode(':', $track_uri);
                if ($tmp[1] == 'local') {
                    displayNotificationWithArtwork($w, 'Cannot remove local track '.$track_name, './images/warning.png', 'Error!');

                    return;
                }
                $ret = removeTrackFromPlaylist($w, $tmp[2], $setting[1], $setting[2]);
                if ($userid != 'vdesabou') {
                    stathat_ez_count('AlfredSpotifyMiniPlayer', 'add_or_remove', 1);
                }
                if ($ret == true) {
                    displayNotificationWithArtwork($w, ''.$track_name.' removed from '.$setting[2].' playlist', $track_artwork_path, 'Remove Track from Playlist');

                    return;
                }
            }
    } elseif ($setting[0] == 'ADD_TO_YOUR_MUSIC') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
            // add track to your music
            if ($track_uri != '') {
                $track_artwork_path = getTrackOrAlbumArtwork($w, $track_uri, true, false, false, $use_artworks);
                $tmp = explode(':', $track_uri);
                if ($tmp[1] == 'local') {
                    // local track, look it up online

                    $query = 'track:'.strtolower($track_name).' artist:'.strtolower($artist_name);
                    $results = searchWebApi($w, $country_code, $query, 'track', 1);

                    if (count($results) > 0) {
                        // only one track returned
                        $track = $results[0];
                        $artists = $track->artists;
                        $artist = $artists[0];
                        logMsg("Unknown track $track_uri / $track_name / $artist_name replaced by track: $track->uri / $track->name / $artist->name");
                        $track_uri = $track->uri;
                        $tmp = explode(':', $track_uri);
                    } else {
                        logMsg("Could not find track: $track_uri / $track_name / $artist_name");
                        displayNotificationWithArtwork($w, 'Local track '.$track_name.' has not online match', './images/warning.png', 'Error!');

                        return;
                    }
                }
                $ret = addTracksToYourMusic($w, $tmp[2], false);
                if ($userid != 'vdesabou') {
                    stathat_ez_count('AlfredSpotifyMiniPlayer', 'add_or_remove', 1);
                }
                if (is_numeric($ret) && $ret > 0) {
                    displayNotificationWithArtwork($w, ''.$track_name.' added to Your Music', $track_artwork_path, 'Add Track to Your Music');

                    return;
                } elseif (is_numeric($ret) && $ret == 0) {
                    displayNotificationWithArtwork($w, ''.$track_name.' is already in Your Music', './images/warning.png', 'Error!');

                    return;
                }
            } // add playlist to your music
            elseif ($playlist_uri != '') {
                $playlist_artwork_path = getPlaylistArtwork($w, $playlist_uri, true, true, $use_artworks);
                $ret = addTracksToYourMusic($w, getThePlaylistTracks($w, $playlist_uri), false);
                if ($userid != 'vdesabou') {
                    stathat_ez_count('AlfredSpotifyMiniPlayer', 'add_or_remove', 1);
                }
                if (is_numeric($ret) && $ret > 0) {
                    displayNotificationWithArtwork($w, 'Playlist '.$playlist_name.' added to Your Music', $playlist_artwork_path, 'Add Playlist to Your Music');

                    return;
                } elseif (is_numeric($ret) && $ret == 0) {
                    displayNotificationWithArtwork($w, 'Playlist '.$playlist_name.' is already in Your Music', './images/warning.png', 'Error!');

                    return;
                }
            } // add album to your music
            elseif ($album_uri != '') {
                $album_artwork_path = getTrackOrAlbumArtwork($w, $album_uri, true, false, false, $use_artworks);
                $ret = addTracksToYourMusic($w, getTheAlbumTracks($w, $album_uri), false);
                if ($userid != 'vdesabou') {
                    stathat_ez_count('AlfredSpotifyMiniPlayer', 'add_or_remove', 1);
                }
                if (is_numeric($ret) && $ret > 0) {
                    displayNotificationWithArtwork($w, 'Album '.$album_name.' added to Your Music', $album_artwork_path, 'Add Album to Your Music');

                    return;
                } elseif (is_numeric($ret) && $ret == 0) {
                    displayNotificationWithArtwork($w, 'Album '.$album_name.' is already in Your Music', './images/warning.png', 'Error!');

                    return;
                }
            }
    } elseif ($setting[0] == 'REMOVE_FROM_YOUR_MUSIC') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
            // remove track from your music
            if ($track_uri != '') {
                $track_artwork_path = getTrackOrAlbumArtwork($w, $track_uri, true, false, false, $use_artworks);
                $tmp = explode(':', $track_uri);
                $ret = removeTrackFromYourMusic($w, $tmp[2]);
                if ($userid != 'vdesabou') {
                    stathat_ez_count('AlfredSpotifyMiniPlayer', 'add_or_remove', 1);
                }
                if ($ret == true) {
                    displayNotificationWithArtwork($w, ''.$track_name.' removed from Your Music', $track_artwork_path, 'Remove Track from Your Music');

                    return;
                }
            }
    } elseif ($setting[0] == 'Open') {
        exec("open \"$setting[1]\"");

        return;
    } elseif ($setting[0] == 'Reveal') {
        exec("open -R \"$setting[1]\"");

        return;
    } elseif ($setting[0] == 'CLEAR_ALFRED_PLAYLIST') {
        if ($setting[1] == '' || $setting[2] == '') {
            displayNotificationWithArtwork($w, 'Alfred Playlist is not set', './images/warning.png', 'Error!');

            return;
        }
        if (clearPlaylist($w, $setting[1], $setting[2])) {
            displayNotificationWithArtwork($w, 'Alfred Playlist '.$setting[2].' was cleared', getPlaylistArtwork($w, $setting[1], true, false, $use_artworks), 'Clear Alfred Playlist');
        }

        return;
    } elseif ($setting[0] == 'SWITCH_USER') {
        
        if($setting[1] == 'NEW_USER') {
            newUser($w);
        } else {
            switchUser($w, $setting[1]);
        }
        

        return;
    } elseif ($setting[0] == 'CHANGE_DEVICE') {
        
        changeUserDevice($w, $setting[1]);
        return;
    } 
} elseif ($other_action != '') {
    if ($other_action == 'disable_all_playlist') {
        $ret = updateSetting($w, 'all_playlists', 0);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Search scope set to Your Music only', './images/search_scope_yourmusic_only.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_all_playlist') {
        $ret = updateSetting($w, 'all_playlists', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Search scope set to your complete library', './images/search.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_now_playing_notifications') {
        $ret = updateSetting($w, 'now_playing_notifications', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Now Playing notifications are now enabled', './images/enable_now_playing.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'disable_now_playing_notifications') {
        $ret = updateSetting($w, 'now_playing_notifications', 0);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Now Playing notifications are now disabled', './images/disable_now_playing.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_quick_mode') {
        $ret = updateSetting($w, 'quick_mode', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Quick Mode is now enabled', './images/enable_quick_mode.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'disable_quick_mode') {
        $ret = updateSetting($w, 'quick_mode', 0);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Quick Mode is now disabled', './images/disable_quick_mode.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_artworks') {
        $ret = updateSetting($w, 'use_artworks', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Artworks are now enabled, library update is started', './images/enable_artworks.png', 'Settings');
            updateLibrary($w);
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'disable_artworks') {
        $ret = updateSetting($w, 'use_artworks', 0);
        if ($ret == true) {
            if (file_exists($w->data().'/artwork')):
                    exec("rm -rf '".$w->data()."/artwork'");
            displayNotificationWithArtwork($w, 'All artworks have been erased', './images/warning.png', 'Warning!');
            updateLibrary($w);
            endif;
            displayNotificationWithArtwork($w, 'Artworks are now disabled', './images/disable_artworks.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_display_rating') {
        $ret = updateSetting($w, 'is_display_rating', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Track Rating is now enabled', './images/enable_display_rating.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'disable_display_rating') {
        $ret = updateSetting($w, 'is_display_rating', 0);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Track Rating is now disabled', './images/disable_display_rating.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_autoplay') {
        $ret = updateSetting($w, 'is_autoplay_playlist', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Playlist Autoplay is now enabled', './images/enable_autoplay.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'disable_autoplay') {
        $ret = updateSetting($w, 'is_autoplay_playlist', 0);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Playlist Autoplay is now disabled', './images/disable_autoplay.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_use_growl') {
        $ret = updateSetting($w, 'use_growl', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Growl is now enabled', './images/enable_use_growl.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'disable_use_growl') {
        $ret = updateSetting($w, 'use_growl', 0);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Growl is now disabled', './images/disable_use_growl.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    }elseif ($other_action == 'enable_always_display_lyrics_in_browser') {
        $ret = updateSetting($w, 'always_display_lyrics_in_browser', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Lyrics will be displayed in browser', './images/lyrics.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'disable_always_display_lyrics_in_browser') {
        $ret = updateSetting($w, 'always_display_lyrics_in_browser', 0);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Lyrics will be displayed in Alfred', './images/lyrics.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'use_twitter') {
        $ret = updateSetting($w, 'use_facebook', 0);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Twitter is now used for sharing', './images/twitter.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'use_facebook') {
        $ret = updateSetting($w, 'use_facebook', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Facebook is now used for sharing', './images/facebook.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_mopidy') {
        exec('./src/spotify_mini_player_notifications.ksh -d "'.$w->data().'" -a stop >> "'.$w->cache().'/action.log" 2>&1 & ');
        $ret = updateSetting($w, 'output_application', 'MOPIDY');
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Mopidy is now used', './images/mopidy.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_applescript') {
        exec('./src/spotify_mini_player_notifications.ksh -d "'.$w->data().'" -a stop >> "'.$w->cache().'/action.log" 2>&1 & ');
        if ($output_application == 'MOPIDY') {
            invokeMopidyMethod($w, 'core.playback.pause', array());
        }
        $ret = updateSetting($w, 'output_application', 'APPLESCRIPT');
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Spotify Desktop is now used', './images/spotify.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_connect') {
        exec('./src/spotify_mini_player_notifications.ksh -d "'.$w->data().'" -a stop >> "'.$w->cache().'/action.log" 2>&1 & ');
        if ($output_application == 'MOPIDY') {
            invokeMopidyMethod($w, 'core.playback.pause', array());
        }
        $ret = updateSetting($w, 'output_application', 'CONNECT');
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Spotify Connect is now used', './images/connect.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'change_theme_color') {

        exec("osascript -e 'tell application \"Alfred 3\" to run trigger \"change_theme_color\" in workflow \"com.vdesabou.spotify.mini.player\" with argument \"\"'");
        return;
    } elseif ($other_action == 'change_search_order') {
        exec("osascript -e 'tell application \"Alfred 3\" to run trigger \"change_search_order\" in workflow \"com.vdesabou.spotify.mini.player\" with argument \"\"'");
        return;
    } elseif ($other_action == 'change_theme_color_for_real') {

        stathat_ez_count('AlfredSpotifyMiniPlayer', 'theme_changed', 1);
        switchThemeColor($w,getenv('chosen_color'));
        return;
    } elseif ($other_action == 'change_search_order_for_real') {

        updateSetting($w, 'search_order', getenv('chosen_search_order'));

        displayNotificationWithArtwork($w, 'Search order results is now changed', './images/search.png', 'Settings');
        return;
    } elseif ($other_action == 'enable_alfred_playlist') {
        $ret = updateSetting($w, 'is_alfred_playlist_active', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Controlling Alfred Playlist', './images/alfred_playlist.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'disable_alfred_playlist') {
        $ret = updateSetting($w, 'is_alfred_playlist_active', 0);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'Controlling Your Music', './images/yourmusic.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'enable_public_playlists') {
        $ret = updateSetting($w, 'is_public_playlists', 1);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'New playlists will be now public', './images/enable_public_playlists.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'disable_public_playlists') {
        $ret = updateSetting($w, 'is_public_playlists', 0);
        if ($ret == true) {
            displayNotificationWithArtwork($w, 'New playlists will be now private', './images/disable_public_playlists.png', 'Settings');
        } else {
            displayNotificationWithArtwork($w, 'Error while updating settings', './images/settings.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'play_track_in_album_context') {
        if ($output_application == 'MOPIDY') {
            playTrackInContextWithMopidy($w, $track_uri, $album_uri);
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to play track \"$track_uri\" in context \"$album_uri\"'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                playTrackSpotifyConnect($w, $device_id, $track_uri, $album_uri);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }
        $album_artwork_path = getTrackOrAlbumArtwork($w, $album_uri, true, false, false, $use_artworks);
        if ($now_playing_notifications == false) {
            displayNotificationWithArtwork($w, '🔈 '.$track_name.' in album '.$album_name.' by '.$artist_name, $album_artwork_path, 'Play Track from Album');
        }
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
        }
        addAlbumToPlayQueue($w, $album_uri, $album_name);

        return;
    } elseif ($other_action == 'play') {
        if ($output_application == 'MOPIDY') {
            invokeMopidyMethod($w, 'core.playback.resume', array());
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to play'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                playSpotifyConnect($w, $device_id);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        return;
    } elseif ($other_action == 'pause') {
        if ($output_application == 'MOPIDY') {
            invokeMopidyMethod($w, 'core.playback.pause', array());
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to pause'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                pauseSpotifyConnect($w, $device_id);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        return;
    } elseif ($other_action == 'playpause') {
        if ($output_application == 'MOPIDY') {
            $state = invokeMopidyMethod($w, 'core.playback.get_state', array());
            if ($state == 'playing') {
                invokeMopidyMethod($w, 'core.playback.pause', array());
            } else {
                invokeMopidyMethod($w, 'core.playback.resume', array());
            }
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to playpause'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                playpauseSpotifyConnect($w, $device_id, $country_code);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        return;
    } elseif ($other_action == 'kill_update') {
        killUpdate($w);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'kill update', 1);
        }

        return;
    } elseif ($other_action == 'lookup_current_artist') {
        lookupCurrentArtist($w);

        return;
    } elseif ($other_action == 'unfollow_playlist') {
        unfollowThePlaylist($w, $playlist_uri);

        return;
    } elseif ($other_action == 'follow_playlist') {
        followThePlaylist($w, $playlist_uri);

        return;
    } elseif ($other_action == 'lyrics') {
        displayLyricsForCurrentTrack($w);

        return;
    } elseif ($other_action == 'current_track_radio') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
        createRadioSongPlaylistForCurrentTrack($w);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'radio', 1);
        }

        return;
    } elseif ($other_action == 'current_artist_radio') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
        createRadioArtistPlaylistForCurrentArtist($w);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'radio', 1);
        }

        return;
    } elseif ($other_action == 'play_current_artist') {
        playCurrentArtist($w);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
        }

        return;
    } elseif ($other_action == 'play_current_album') {
        playCurrentAlbum($w);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
        }

        return;
    } elseif ($other_action == 'delete_artwork_folder') {
        if (file_exists($w->data().'/artwork')):
                exec("rm -rf '".$w->data()."/artwork'");
        displayNotificationWithArtwork($w, 'All artworks have been erased', './images/warning.png', 'Warning!');
        updateLibrary($w);
        endif;

        return;
    } elseif ($other_action == 'Oauth_Login') {
        // check PHP version
        $version = explode('.', phpversion());
        if ($version[0] < 5 && $version[1] < 4) {
            displayNotificationWithArtwork($w, 'PHP 5.4.0 or later is required for authentication', './images/warning.png', 'Error!');
            exec('open http://alfred-spotify-mini-player.com/known-issues/#php_requirement');

            return;
        }
        exec("kill -9 $(ps -efx | grep \"php\" | egrep \"php -S 127.0.0.1:15298\" | grep -v grep | awk '{print $2}')");
        sleep(1);
        $cache_log = $w->cache().'/spotify_mini_player_web_server.log';
        exec("php -S 127.0.0.1:15298 > \"$cache_log\" 2>&1 &");
        sleep(2);
        exec('open http://127.0.0.1:15298');

        return;
    } elseif ($other_action == 'current') {
        displayNotificationForCurrentTrack($w);
        if ($type != 'playing') {
            updateCurrentTrackIndexFromPlayQueue($w);
        }

        return;
    } elseif ($other_action == 'current_mopidy') {
        $ret = getCurrentTrackInfoWithMopidy($w, false);
        echo "$ret";

        return;
    } elseif ($other_action == 'current_connect') {
        $ret = getCurrentTrackInfoWithSpotifyConnect($w, false);
        echo "$ret";

        return;
    } elseif ($other_action == 'add_current_track_to') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
        addCurrentTrackTo($w);

        return;
    } elseif ($other_action == 'remove_current_track_from') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
        removeCurrentTrackFrom($w);

        return;
    } elseif ($other_action == 'download_update') {
        $check_results = checkForUpdate($w, 0, true);
        if ($check_results != null && is_array($check_results)) {
            exec("open \"$check_results[1]\"");
            displayNotificationWithArtwork($w, 'Please install the new version with Alfred', './images/check_update.png', 'Update available');

            return;
        }

        return;
    } elseif ($other_action == 'previous') {
        if ($output_application == 'MOPIDY') {
            invokeMopidyMethod($w, 'core.playback.previous', array());
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to previous track'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                previousTrackSpotifyConnect($w, $device_id);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        return;
    } elseif ($other_action == 'next') {
        if ($output_application == 'MOPIDY') {
            invokeMopidyMethod($w, 'core.playback.next', array());
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to next track'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                nextTrackSpotifyConnect($w, $device_id);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        return;
    } elseif ($other_action == 'add_current_track') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
        addCurrentTrackToAlfredPlaylistOrYourMusic($w);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'add_or_remove', 1);
        }

        return;
    } elseif ($other_action == 'random') {
        list($track_uri, $track_name, $artist_name, $album_name, $duration) = getRandomTrack($w);
        if ($output_application == 'MOPIDY') {
            playUriWithMopidyWithoutClearing($w, $track_uri);
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to play track \"$track_uri\"'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                playTrackSpotifyConnect($w, $device_id, $track_uri, '');
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
        }
        addTrackToPlayQueue($w, $track_uri, $track_name, $artist_name, $album_name, $duration, $country_code);

        return;
    } elseif ($other_action == 'random_album') {
        list($album_uri, $album_name, $theartistname) = getRandomAlbum($w);
        if ($output_application == 'MOPIDY') {
            playUriWithMopidy($w, $album_uri);
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to play track \"$album_uri\"'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                playTrackSpotifyConnect($w, $device_id, '', $album_uri);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }
        displayNotificationWithArtwork($w, '🔈 Album '.$album_name.' by '.$theartistname, getTrackOrAlbumArtwork($w, $album_uri, true, false, false, $use_artworks), 'Play Random Album');
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
        }
        addAlbumToPlayQueue($w, $album_uri, $album_name);

        return;
    } elseif ($other_action == 'reset_settings') {
        deleteTheFile($w->data().'/settings.json');

        return;
    } elseif ($other_action == 'reset_oauth_settings') {
        updateSetting($w,'oauth_access_token','');
        updateSetting($w,'oauth_refresh_token','');
        displayNotificationWithArtwork($w, 'Oauth settings have been correctly reset', './images/settings.png', 'Info');

        return;
    } elseif ($other_action == 'biography') {
        displayCurrentArtistBiography($w);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'display biography', 1);
        }

        return;
    } elseif ($other_action == 'go_back') {
        $history = $w->read('history.json');

        if ($history == false) {
            displayNotificationWithArtwork($w, 'No history yet', './images/warning.png', 'Error!');
        }
        $query = array_pop($history);
            // pop twice
            $query = array_pop($history);
        $w->write($history, 'history.json');
        exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini')." $query\"'");

        return;
    } elseif ($other_action == 'lookup_artist') {
        if (!$w->internet()) {
            displayNotificationWithArtwork($w, 'No internet connection', './images/warning.png', 'Error!');

            return;
        }
        if ($artist_uri == '') {
            $artist_uri = getArtistUriFromTrack($w, $track_uri);
        }
        exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Online▹'.$artist_uri.'@'.escapeQuery($artist_name).'▹'."\"'");
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'lookup online', 1);
        }

        return;
    } elseif ($other_action == 'playartist') {
        $artist_artwork_path = getArtistArtwork($w, $artist_uri, $artist_name, true, false, false, $use_artworks);
        if ($output_application == 'MOPIDY') {
            playUriWithMopidy($w, $artist_uri);
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to play track \"$artist_uri\"'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                playTrackSpotifyConnect($w, $device_id, '', $artist_uri);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }
        displayNotificationWithArtwork($w, '🔈 Artist '.$artist_name, $artist_artwork_path, 'Play Artist');
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
        }
        addArtistToPlayQueue($w, $artist_uri, $artist_name, $country_code);

        return;
    } elseif ($other_action == 'playalbum') {
        if ($album_uri == '') {
            if ($track_uri == '') {
                displayNotificationWithArtwork($w, 'Cannot get current album', './images/warning.png', 'Error!');

                return;
            }
            $album_uri = getAlbumUriFromTrack($w, $track_uri);
            if ($album_uri == false) {
                displayNotificationWithArtwork($w, 'Cannot get album', './images/warning.png', 'Error!');

                return;
            }
        }
        $album_artwork_path = getTrackOrAlbumArtwork($w, $album_uri, true, false, false, $use_artworks);
        if ($output_application == 'MOPIDY') {
            playUriWithMopidy($w, $album_uri);
        } else if($output_application == 'APPLESCRIPT') {
            exec("osascript -e 'tell application \"Spotify\" to play track \"$album_uri\"'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                playTrackSpotifyConnect($w, $device_id, '', $album_uri);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }
        displayNotificationWithArtwork($w, '🔈 Album '.$album_name, $album_artwork_path, 'Play Album');
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
        }
        addAlbumToPlayQueue($w, $album_uri, $album_name);

        return;
    } elseif ($other_action == 'volume_up') {
        if ($output_application == 'MOPIDY') {
            $theVolume = invokeMopidyMethod($w, 'core.mixer.get_volume', array());
            if (($theVolume + $volume_percent) > getenv('settings_volume_max')) {
                $theVolume = getenv('settings_volume_max');
                $theVolume = $theVolume + 0;
                displayNotificationWithArtwork($w, 'Spotify volume is at maximum level '.getenv('settings_volume_max').'%.', './images/volume_up.png', 'Volume Up');
            } else {
                $theVolume = $theVolume + $volume_percent;
                displayNotificationWithArtwork($w, 'Spotify volume has been increased to '.$theVolume.'%', './images/volume_up.png', 'Volume Up');
            }
            invokeMopidyMethod($w, 'core.mixer.set_volume', array('volume' => $theVolume));
        } else if($output_application == 'APPLESCRIPT') {
            $volume_max = getenv('settings_volume_max');
            $command_output = exec("osascript -e 'tell application \"Spotify\"
				if it is running then
					if (sound volume + $volume_percent) > $volume_max then
						set theVolume to $volume_max
						set sound volume to theVolume
						return \"Spotify volume is at maximum level \" & theVolume & \"%.\"
					else
						set theVolume to (sound volume + $volume_percent)
						set sound volume to theVolume
						return \"Spotify volume has been increased to \" & theVolume & \"%.\"
					end if
				end if
			end tell'");
            displayNotificationWithArtwork($w, $command_output, './images/volume_up.png', 'Volume Up');
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                $theVolume = getVolumeSpotifyConnect($w,$device_id);
                if(!$theVolume) {
                    displayNotificationWithArtwork($w, 'Cannot control volume on this Spotify Connect device', './images/warning.png', 'Error!');
                    return;
                }
                if (($theVolume + $volume_percent) > getenv('settings_volume_max')) {
                    $theVolume = getenv('settings_volume_max');
                    $theVolume = $theVolume + 0;
                    displayNotificationWithArtwork($w, 'Spotify volume is at maximum level '.getenv('settings_volume_max').'%.', './images/volume_up.png', 'Volume Up');
                } else {
                    $theVolume = $theVolume + $volume_percent;
                    displayNotificationWithArtwork($w, 'Spotify volume has been increased to '.$theVolume.'%', './images/volume_up.png', 'Volume Up');
                }                
                changeVolumeSpotifyConnect($w, $device_id, $theVolume);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        return;
    } elseif ($other_action == 'volume_down') {
        if ($output_application == 'MOPIDY') {
            $theVolume = invokeMopidyMethod($w, 'core.mixer.get_volume', array());
            if (($theVolume - $volume_percent) < getenv('settings_volume_min')) {
                $theVolume = getenv('settings_volume_min');
                displayNotificationWithArtwork($w, 'Spotify volume is at minimum level '.getenv('settings_volume_min').'%.', './images/volume_down.png', 'Volume Down');
            } else {
                $theVolume = $theVolume - $volume_percent;
                displayNotificationWithArtwork($w, 'Spotify volume has been decreased to '.$theVolume.'%', './images/volume_down.png', 'Volume Down');
            }
            invokeMopidyMethod($w, 'core.mixer.set_volume', array('volume' => $theVolume));
        } else if($output_application == 'APPLESCRIPT') {
            $volume_min = getenv('settings_volume_min');
            $command_output = exec("osascript -e 'tell application \"Spotify\"
				if it is running then
					if (sound volume - $volume_percent) < $volume_min then
						set theVolume to $volume_min
						set sound volume to theVolume
						return \"Spotify volume is at minimum level \" & theVolume & \"%.\"
					else
						set theVolume to (sound volume - $volume_percent)
						set sound volume to theVolume
						return \"Spotify volume has been decreased to \" & theVolume & \"%.\"
					end if
					set sound volume to theVolume
				end if
			end tell'");
            displayNotificationWithArtwork($w, $command_output, './images/volume_down.png', 'Volume Down');
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                $theVolume = getVolumeSpotifyConnect($w,$device_id);
                if(!$theVolume) {
                    displayNotificationWithArtwork($w, 'Cannot control volume on this Spotify Connect device', './images/warning.png', 'Error!');
                    return;
                }
                if (($theVolume - $volume_percent) < getenv('settings_volume_min')) {
                    $theVolume = getenv('settings_volume_min');
                    displayNotificationWithArtwork($w, 'Spotify volume is at minimum level '.getenv('settings_volume_min').'%.', './images/volume_down.png', 'Volume Down');
                } else {
                    $theVolume = $theVolume - $volume_percent;
                    displayNotificationWithArtwork($w, 'Spotify volume has been decreased to '.$theVolume.'%', './images/volume_down.png', 'Volume Down');
                }               
                changeVolumeSpotifyConnect($w, $device_id, $theVolume);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }

        return;
    } elseif ($other_action == 'volmax') {
        if ($output_application == 'MOPIDY') {
            invokeMopidyMethod($w, 'core.mixer.set_volume', array('volume' => getenv('settings_volume_max')));
        } else if($output_application == 'APPLESCRIPT') {
            $volume_max = getenv('settings_volume_max');
            exec("osascript -e 'tell application \"Spotify\"
				if it is running then
					set sound volume to $volume_max
				end if
			end tell'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                $theVolume = getVolumeSpotifyConnect($w,$device_id);
                if(!$theVolume) {
                    displayNotificationWithArtwork($w, 'Cannot control volume on this Spotify Connect device', './images/warning.png', 'Error!');
                    return;
                }
                $volume_max = getenv('settings_volume_max');              
                changeVolumeSpotifyConnect($w, $device_id, $volume_max);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }
        displayNotificationWithArtwork($w, 'Spotify volume has been set to maximum '.$volume_max.'%', './images/volmax.png', 'Volume Max');

        return;
    } elseif ($other_action == 'volmid') {
        if ($output_application == 'MOPIDY') {
            invokeMopidyMethod($w, 'core.mixer.set_volume', array('volume' => getenv('settings_volume_mid')));
        } else if($output_application == 'APPLESCRIPT') {
            $volume_mid = getenv('settings_volume_mid');
            exec("osascript -e 'tell application \"Spotify\"
				if it is running then
					set sound volume to $volume_mid
				end if
			end tell'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                $theVolume = getVolumeSpotifyConnect($w,$device_id);
                if(!$theVolume) {
                    displayNotificationWithArtwork($w, 'Cannot control volume on this Spotify Connect device', './images/warning.png', 'Error!');
                    return;
                }
                $volume_mid = getenv('settings_volume_mid');             
                changeVolumeSpotifyConnect($w, $device_id, $volume_mid);
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }
        displayNotificationWithArtwork($w, 'Spotify volume has been set to '.$volume_mid.'%', './images/volmid.png', 'Volume '.$volume_mid.'%');

        return;
    } elseif ($other_action == 'mute') {
        $volume_max = getenv('settings_volume_max');
        $volume_min = getenv('settings_volume_min');
        if ($output_application == 'MOPIDY') {
            $volume = invokeMopidyMethod($w, 'core.mixer.get_volume', array());
            if ($volume <= 0) {
                invokeMopidyMethod($w, 'core.mixer.set_volume', array('volume' => $volume_max));
                $command_output = 'Spotify volume is unmuted.';
            } else {
                invokeMopidyMethod($w, 'core.mixer.set_volume', array('volume' => $volume_min));
                $command_output = 'Spotify volume is muted.';
            }
        } else if($output_application == 'APPLESCRIPT') {
            $command_output = exec("osascript -e 'tell application \"Spotify\"
				if sound volume is less than or equal to $volume_min then
					set sound volume to $volume_max
					return \"Spotify volume is unmuted.\"
				else
					set sound volume to $volume_min
					return \"Spotify volume is muted.\"
				end if
			end tell'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                $theVolume = getVolumeSpotifyConnect($w,$device_id);
                if(!$theVolume) {
                    displayNotificationWithArtwork($w, 'Cannot control volume on this Spotify Connect device', './images/warning.png', 'Error!');
                    return;
                }
                if ($theVolume <= 0) {
                    changeVolumeSpotifyConnect($w, $device_id, $volume_max);
                    $command_output = 'Spotify volume is unmuted.';
                } else {
                    if($volume_min == 0) {
                        // there is a bug that if we set 0 then volume
                        // control is no more possible
                        $volume_min = 5;
                    }
                    changeVolumeSpotifyConnect($w, $device_id, $volume_min);
                    $command_output = 'Spotify volume is muted.';
                }           
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }
        displayNotificationWithArtwork($w, $command_output, './images/mute.png', 'Mute');

        return;
    } elseif ($other_action == 'shuffle') {
        if ($output_application == 'MOPIDY') {
            $isShuffleEnabled = invokeMopidyMethod($w, 'core.tracklist.get_random', array());
            if ($isShuffleEnabled) {
                invokeMopidyMethod($w, 'core.tracklist.set_random', array('value' => false));
                $command_output = 'Shuffle is now disabled.';
            } else {
                invokeMopidyMethod($w, 'core.tracklist.set_random', array('value' => true));
                $command_output = 'Shuffle is now enabled.';
            }
        } else if($output_application == 'APPLESCRIPT') {
            $command_output = exec("osascript -e '
		tell application \"Spotify\"
		if shuffling enabled is true then
			if shuffling is true then
				set shuffling to false
				return \"Shuffle is now disabled.\"
			else
				set shuffling to true
				return \"Shuffle is now enabled.\"
			end if
		else
			return \"Shuffle is not currently enabled.\"
		end if
		end tell'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                if (isShuffleActive(false) == 'true') {
                    setShuffleStateSpotifyConnect($w, $device_id, false);
                    $command_output = 'Shuffle is now disabled.'.$ret ;
                } else {
                    setShuffleStateSpotifyConnect($w, $device_id, true);
                    $command_output = 'Shuffle is now enabled.';
                }
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }
        displayNotificationWithArtwork($w, $command_output, './images/shuffle.png', 'Shuffle');

        return;
    } elseif ($other_action == 'web_search') {
        if ($output_application == 'MOPIDY') {
            $ret = getCurrentTrackInfoWithMopidy($w, false);
            $results = explode('▹', $ret);
        } else if($output_application == 'APPLESCRIPT') {
            // get info on current song
            exec('./src/track_info.ksh 2>&1', $retArr, $retVal);

            if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
                $results = explode('▹', $retArr[count($retArr) - 1]);
            }
        } else {
            $ret = getCurrentTrackInfoWithSpotifyConnect($w, false);
            $results = explode('▹', $ret);
        }

        if (!isset($results[0])) {
            displayNotificationWithArtwork($w, 'Cannot get current track', './images/warning.png', 'Error!');
            return;
        }
        $search_text = escapeQuery($results[0]);
        $search_text .= '▹';
        $search_text .= escapeQuery($results[1]);

        exec("osascript -e 'tell application \"Alfred 3\" to run trigger \"web_search\" in workflow \"com.vdesabou.spotify.mini.player\" with argument \"" . $search_text . "\"'");
        return;
    } elseif ($other_action == 'share') {
        if ($output_application == 'MOPIDY') {
            $ret = getCurrentTrackInfoWithMopidy($w, false);
            $results = explode('▹', $ret);
        } else if($output_application == 'APPLESCRIPT') {
            // get info on current song
            exec('./src/track_info.ksh 2>&1', $retArr, $retVal);

            if (substr_count($retArr[count($retArr) - 1], '▹') > 0) {
                $results = explode('▹', $retArr[count($retArr) - 1]);
            }
        } else {
            $ret = getCurrentTrackInfoWithSpotifyConnect($w, false);
            $results = explode('▹', $ret);
        }

        if (!isset($results[0])) {
            displayNotificationWithArtwork($w, 'Cannot get current track', './images/warning.png', 'Error!');
            return;
        }
        
        if($use_facebook) {
           $service = 'facebook'; 
        } else {
           $service = 'twitter';  
        }
        $text = getenv('sharing_hashtag1');
        $text .= ' ';
        $text .= escapeQuery($results[0]);
        $text .= ' by ';
        $text .= escapeQuery($results[1]);
        $text .= ' ';
        if(getenv('sharing_text1') != '') {
            $text .= getenv('sharing_text1');
            $text .= ' ';
        }
        if(getenv('sharing_text2') != '') {
            $text .= getenv('sharing_text2');
            $text .= ' ';
        }
        if(getenv('sharing_hashtag2') != '') {
            $text .= getenv('sharing_hashtag2');
            $text .= ' ';
        }    

        $tmp = explode(':', $results[4]);
        if ($tmp[1] != 'local') {
            $text .= ' https://open.spotify.com/track/';
            $text .= $tmp[2];
        }
        exec("./terminal-share.app/Contents/MacOS/terminal-share -service '".$service."' -text '".$text."'");

        return;
    } elseif ($other_action == 'repeating') {
        if ($output_application == 'MOPIDY') {
            $isRepeatingEnabled = invokeMopidyMethod($w, 'core.tracklist.get_repeat', array());
            if ($isRepeatingEnabled) {
                invokeMopidyMethod($w, 'core.tracklist.set_repeat', array('value' => false));
                $command_output = 'Repeating is now disabled.';
            } else {
                invokeMopidyMethod($w, 'core.tracklist.set_repeat', array('value' => true));
                $command_output = 'Repeating is now enabled.';
            }
        } else if($output_application == 'APPLESCRIPT') {
            $command_output = exec("osascript -e '
		tell application \"Spotify\"
		if repeating enabled is true then
			if repeating is true then
				set repeating to false
				return \"Repeating is now disabled.\"
			else
				set repeating to true
				return \"Repeating is now enabled.\"
			end if
		else
			return \"Repeating is not currently enabled.\"
		end if
		end tell'");
        } else {
            $device_id = getSpotifyConnectCurrentDeviceId($w);
            if($device_id != '') {
                if (isRepeatStateSpotifyConnectActive($w)) {
                    setRepeatStateSpotifyConnect($w, $device_id, false);
                    $command_output = 'Repeating is now disabled.';
                } else {
                    setRepeatStateSpotifyConnect($w, $device_id, true);
                    $command_output = 'Repeating is now enabled.';
                }
            } else {
                displayNotificationWithArtwork($w, 'No Spotify Connect device is available', './images/warning.png', 'Error!');
            }
        }
        displayNotificationWithArtwork($w, $command_output, './images/repeating.png', 'Repeating');

        return;
    } elseif ($other_action == 'spot_mini_debug') {
        createDebugFile($w);

        return;
    } elseif ($other_action == 'radio_artist') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
        createRadioArtistPlaylist($w, $artist_name, $artist_uri);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'radio', 1);
        }

        return;
    } elseif ($other_action == 'complete_collection_artist') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
        createCompleteCollectionArtistPlaylist($w, $artist_name, $artist_uri);

        return;
    } elseif ($other_action == 'show_alfred_playlist') {

        if($alfred_playlist_uri != '' ) {
            exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Playlist▹'.$alfred_playlist_uri.'▹'."\"'");
        } else {
            displayNotificationWithArtwork($w, 'Alfred Playlist is not set', './images/warning.png', 'Error!');
        }

        return;
    } elseif ($other_action == 'play_alfred_playlist') {
        playAlfredPlaylist($w);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'play', 1);
        }

        return;
    } elseif ($other_action == 'update_library') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
        updateLibrary($w);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'update library', 1);
        }

        return;
    } elseif ($other_action == 'refresh_library') {
        if (file_exists($w->data().'/update_library_in_progress')) {
            displayNotificationWithArtwork($w, 'Cannot modify library while update is in progress', './images/warning.png', 'Error!');

            return;
        }
        refreshLibrary($w);
        if ($userid != 'vdesabou') {
            stathat_ez_count('AlfredSpotifyMiniPlayer', 'update library', 1);
        }

        return;
    }
}
