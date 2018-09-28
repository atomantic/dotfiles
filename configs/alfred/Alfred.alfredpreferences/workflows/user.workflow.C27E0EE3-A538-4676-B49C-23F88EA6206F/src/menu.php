<?php

/**
 * oAuthChecks function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $update_in_progress
 */
function oAuthChecks($w, $query, $settings, $update_in_progress)
{
    $words = explode('▹', $query);
    $kind = $words[0];

    $all_playlists = $settings->all_playlists;
    $is_alfred_playlist_active = $settings->is_alfred_playlist_active;
    $radio_number_tracks = $settings->radio_number_tracks;
    $now_playing_notifications = $settings->now_playing_notifications;
    $max_results = $settings->max_results;
    $alfred_playlist_uri = $settings->alfred_playlist_uri;
    $alfred_playlist_name = $settings->alfred_playlist_name;
    $country_code = $settings->country_code;
    $last_check_update_time = $settings->last_check_update_time;
    $oauth_client_id = $settings->oauth_client_id;
    $oauth_client_secret = $settings->oauth_client_secret;
    $oauth_redirect_uri = $settings->oauth_redirect_uri;
    $oauth_access_token = $settings->oauth_access_token;
    $oauth_expires = $settings->oauth_expires;
    $oauth_refresh_token = $settings->oauth_refresh_token;
    $display_name = $settings->display_name;
    $userid = $settings->userid;

    ////
    // OAUTH checks
    // Check oauth config : Client ID and Client Secret
    if ($oauth_client_id == '' && substr_count($query, '▹') == 0) {
        if (mb_strlen($query) == 0) {
            $w->result(null, '', 'Your Application Client ID is missing', 'Get it from your Spotify Application and copy/paste it here', './images/settings.png', 'no', null, '');
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'Open▹'.'https://developer.spotify.com/my-applications/#!/applications' /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Open Spotify Application page to get required information', 'This will open the Application page with your default browser', './images/spotify.png', 'yes', null, '');
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'Open▹'.'http://alfred-spotify-mini-player.com/setup/' /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Go to the website alfred-spotify-mini-player.com to see setup tutorial', 'This will open the Application page with your default browser', './images/website.png', 'yes', null, '');

            listUsers($w);
        } elseif (mb_strlen($query) != 32) {
            $w->result(null, '', 'The Application Client ID does not seem valid!', 'The length is not 32. Make sure to copy the Client ID from https://developer.spotify.com/my-applications', './images/warning.png', 'no', null, '');
        } else {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'Oauth_Client_ID▹'.rtrim(ltrim($query)) /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Application Client ID will be set to <'.rtrim(ltrim($query)).'>', 'Type enter to validate the Application Client ID', './images/settings.png', 'yes', null, '');
        }
        echo $w->tojson();
        exit;
    }

    if ($oauth_client_secret == '' && substr_count($query, '▹') == 0) {
        if (mb_strlen($query) == 0) {
            $w->result(null, '', 'Your Application Client Secret is missing!', 'Get it from your Spotify Application and enter it here', './images/settings.png', 'no', null, '');
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'Open▹'.'https://developer.spotify.com/my-applications/#!/applications' /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Open Spotify Application page to get required information', 'This will open the Application page with your default browser', './images/spotify.png', 'yes', null, '');
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'Open▹'.'http://alfred-spotify-mini-player.com/setup/' /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Go to the website alfred-spotify-mini-player.com to see setup tutorial', 'This will open the Application page with your default browser', './images/website.png', 'yes', null, '');

            listUsers($w);
        } elseif (mb_strlen($query) != 32) {
            $w->result(null, '', 'The Application Client Secret does not seem valid!', 'The length is not 32. Make sure to copy the Client Secret from https://developer.spotify.com/my-applications', './images/warning.png', 'no', null, '');
        } elseif ($query == $oauth_client_id) {
            $w->result(null, '', 'The Application Client Secret entered is the same as Application Client ID, this is wrong!', 'Make sure to copy the Client Secret from https://developer.spotify.com/my-applications', './images/warning.png', 'no', null, '');
        } else {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'Oauth_Client_SECRET▹'.rtrim(ltrim($query)) /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Application Client Secret will be set to <'.rtrim(ltrim($query)).'>', 'Type enter to validate the Application Client Secret', './images/settings.png', 'yes', null, '');
        }
        echo $w->tojson();
        exit;
    }

    if ($oauth_access_token == '' && substr_count($query, '▹') == 0) {
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'Oauth_Login' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Authenticate to Spotify', array(
                'This will start the authentication process',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
            ), './images/settings.png', 'yes', null, '');
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    'Open▹'.'http://alfred-spotify-mini-player.com/setup/' /* other_settings*/,
                    '' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Go to the website alfred-spotify-mini-player.com to see setup tutorial', 'This will open the Application page with your default browser', './images/website.png', 'yes', null, '');

        listUsers($w);
        
        echo $w->tojson();
        exit;
    }
}

/**
 * mainMenu function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function mainMenu($w, $query, $settings, $db, $update_in_progress)
{
    $words = explode('▹', $query);
    $kind = $words[0];

    $all_playlists = $settings->all_playlists;
    $is_alfred_playlist_active = $settings->is_alfred_playlist_active;
    $radio_number_tracks = $settings->radio_number_tracks;
    $now_playing_notifications = $settings->now_playing_notifications;
    $max_results = $settings->max_results;
    $alfred_playlist_uri = $settings->alfred_playlist_uri;
    $alfred_playlist_name = $settings->alfred_playlist_name;
    $country_code = $settings->country_code;
    $last_check_update_time = $settings->last_check_update_time;
    $oauth_client_id = $settings->oauth_client_id;
    $oauth_client_secret = $settings->oauth_client_secret;
    $oauth_redirect_uri = $settings->oauth_redirect_uri;
    $oauth_access_token = $settings->oauth_access_token;
    $oauth_expires = $settings->oauth_expires;
    $oauth_refresh_token = $settings->oauth_refresh_token;
    $display_name = $settings->display_name;
    $userid = $settings->userid;
    $use_artworks = $settings->use_artworks;
    $output_application = $settings->output_application;
    $quick_mode = $settings->quick_mode;

    ////////

    // MAIN MENU
    //////////////
    $getCounters = 'select * from counters';
    try {
        $stmt = $db->prepare($getCounters);

        $counters = $stmt->execute();
        $counter = $stmt->fetch();
    } catch (PDOException $e) {
        handleDbIssuePdoXml($db);

        exit;
    }

    $all_tracks = $counter[0];
    $yourmusic_tracks = $counter[1];
    $all_artists = $counter[2];
    $yourmusic_artists = $counter[3];
    $all_albums = $counter[4];
    $yourmusic_albums = $counter[5];
    $nb_playlists = $counter[6];

    if ($update_in_progress == true) {
        $in_progress_data = $w->read('update_library_in_progress');
        $update_library_in_progress_words = explode('▹', $in_progress_data);
        $elapsed_time = time() - $update_library_in_progress_words[3];
        if (startsWith($update_library_in_progress_words[0], 'Init')) {
            $w->result(null, $w->data().'/update_library_in_progress', 'Initialization phase since '.beautifyTime($elapsed_time, true).' : '.floatToSquares(0), 'Waiting for Spotify servers to return required data', './images/update_in_progress.png', 'no', null, '');
        } else {
            if ($update_library_in_progress_words[0] == 'Refresh Library') {
                $type = 'playlists';
            } elseif ($update_library_in_progress_words[0] == 'Artists') {
                $type = 'artists';
            } else {
                $type = 'tracks';
            }

            if ($update_library_in_progress_words[2] != 0) {
                $w->result(null, $w->data().'/update_library_in_progress', $update_library_in_progress_words[0].' update in progress since '.beautifyTime($elapsed_time, true).' : '.floatToSquares(intval($update_library_in_progress_words[1]) / intval($update_library_in_progress_words[2])), $update_library_in_progress_words[1].'/'.$update_library_in_progress_words[2].' '.$type.' processed so far. Currently processing <'.$update_library_in_progress_words[4].'>', './images/update_in_progress.png', 'no', null, '');
            } else {
                $w->result(null, $w->data().'/update_library_in_progress', $update_library_in_progress_words[0].' update in progress since '.beautifyTime($elapsed_time, true).' : '.floatToSquares(0), 'No '.$type.' processed so far', './images/update_in_progress.png', 'no', null, '');
            }
        }
    }
    $quick_mode_text = '';
    if ($quick_mode) {
        $quick_mode_text = ' ● ⚡ Quick Mode is active';
    }
    if ($all_playlists == true) {
        $w->result(null, '', 'Search for music in "Your Music" and your '.$nb_playlists.' playlists', 'Begin typing at least 3 characters to start search in your '.$all_tracks.' tracks'.$quick_mode_text, './images/search.png', 'no', null, '');
    } else {
        $w->result(null, '', 'Search for music in "Your Music" only', 'Begin typing at least 3 characters to start search in your '.$yourmusic_tracks.' tracks'.$quick_mode_text, './images/search_scope_yourmusic_only.png', 'no', null, '');
    }

    if(getenv('menu_display_current_track') == 1) {
        $w->result(null, '', 'Current Track', 'Display current track information and browse various options', './images/current_track.png', 'no', null, 'Current Track▹');
    }

    if($output_application == 'CONNECT') {
        if(getenv('menu_display_spotify_connect') == 1) {
            $w->result(null, '', 'Spotify Connect', 'Display Spotify Connect devices', './images/connect.png', 'no', null, 'Spotify Connect▹');
        }
    }

    if(getenv('menu_display_play_queue') == 1) {
        $w->result(null, '', 'Play Queue', 'Get the current play queue. Always use the workflow to launch tracks, otherwise play queue will be empty', './images/play_queue.png', 'no', null, 'Play Queue▹');
    }

    if(getenv('menu_display_lookup_current_artist_online') == 1) {
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'lookup_current_artist' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Lookup Current Artist online', array(
                '☁︎ Query all albums/tracks from current artist online..',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
            ), './images/online_artist.png', 'yes', '');
    }

    if(getenv('menu_display_search_online') == 1) {
        $w->result(null, '', 'Search online', '☁︎ You can search tracks, artists, albums and playlists online, i.e not in your library', './images/online.png', 'no', null, 'Search Online▹');
    }

    if(getenv('menu_display_alfred_playlist') == 1) {
        if ($is_alfred_playlist_active == true) {
            if ($alfred_playlist_name != '') {

                $r = explode(':', $alfred_playlist_uri);

                $w->result(null, '', 'Browse your Alfred playlist ('.$alfred_playlist_name.' by '.$r[2].')', 'You can change the Alfred Playlist during next step', getPlaylistArtwork($w, $alfred_playlist_uri, false, false, $use_artworks), 'no', null, 'Playlist▹'.$alfred_playlist_uri.'▹');
            } else {
                $title = 'Alfred Playlist ● not set';
                $w->result(null, '', $title, 'Choose one of your playlists and add tracks, album, playlist to it directly from the workflow', './images/alfred_playlist.png', 'no', null, 'Alfred Playlist▹Set Alfred Playlist▹');
            }
        }
    }

    if(getenv('menu_display_your_recent_tracks') == 1) {
        $w->result(null, '', 'Your Recent Tracks', 'Browse your recent tracks', './images/recent.png', 'no', null, 'Recent Tracks▹');
    }
    if(getenv('menu_display_browse_by_playlist') == 1) {
        $w->result(null, '', 'Playlists', 'Browse by playlist'.' ('.$nb_playlists.' playlists)', './images/playlists.png', 'no', null, 'Playlist▹');
    }
    if(getenv('menu_display_browse_your_music') == 1) {
        $w->result(null, '', 'Your Music', 'Browse Your Music'.' ('.$yourmusic_tracks.' tracks ● '.$yourmusic_albums.'  albums ● '.$yourmusic_artists.' artists)', './images/yourmusic.png', 'no', null, 'Your Music▹');
    }
    if ($all_playlists == true) {
        if(getenv('menu_display_browse_by_artist') == 1) {
            $w->result(null, '', 'Artists', 'Browse by artist'.' ('.$all_artists.' artists)', './images/artists.png', 'no', null, 'Artist▹');
        }
        if(getenv('menu_display_browse_by_album') == 1) {
            $w->result(null, '', 'Albums', 'Browse by album'.' ('.$all_albums.' albums)', './images/albums.png', 'no', null, 'Album▹');
        }
    } else {
        if(getenv('menu_display_browse_by_artist') == 1) {
            $w->result(null, '', 'Artists in "Your Music"', 'Browse by artist'.' ('.$yourmusic_artists.' artists)', './images/artists.png', 'no', null, 'Artist▹');
        }
        if(getenv('menu_display_browse_by_album') == 1) {
            $w->result(null, '', 'Albums in "Your Music"', 'Browse by album'.' ('.$yourmusic_albums.' albums)', './images/albums.png', 'no', null, 'Album▹');
        }
    }

    if(getenv('menu_display_browse_categories') == 1) {
        $w->result(null, '', 'Browse', 'Browse Spotify by categories, as in the Spotify player’s “Browse” tab', './images/browse.png', 'no', null, 'Browse▹');
    }
    if(getenv('menu_display_your_tops') == 1) {
        $w->result(null, '', 'Your Tops', 'Browse your top artists and top tracks', './images/star.png', 'no', null, 'Your Tops▹');
    }

    if ($is_alfred_playlist_active == true) {
        $alfred_playlist_state = 'Alfred Playlist';
    } else {
        $alfred_playlist_state = 'Your Music';
    }
    if ($all_playlists == true) {
        $w->result(null, '', 'Settings', 'User='.$userid.', Search scope=<All>, Max results=<'.$max_results.'>, Controlling <'.$alfred_playlist_state.'>, Radio tracks=<'.$radio_number_tracks.'>', './images/settings.png', 'no', null, 'Settings▹');
    } else {
        $w->result(null, '', 'Settings', 'User='.$userid.', Search scope=<Your Music>, Max results=<'.$max_results.'>, Controlling <'.$alfred_playlist_state.'>, Radio tracks=<'.$radio_number_tracks.'>', './images/settings.png', 'no', null, 'Settings▹');
    }
}

/**
 * mainSearch function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function mainSearch($w, $query, $settings, $db, $update_in_progress)
{
    $words = explode('▹', $query);
    $kind = $words[0];

    $all_playlists = $settings->all_playlists;
    $is_alfred_playlist_active = $settings->is_alfred_playlist_active;
    $radio_number_tracks = $settings->radio_number_tracks;
    $now_playing_notifications = $settings->now_playing_notifications;
    $max_results = $settings->max_results;
    $alfred_playlist_uri = $settings->alfred_playlist_uri;
    $alfred_playlist_name = $settings->alfred_playlist_name;
    $country_code = $settings->country_code;
    $last_check_update_time = $settings->last_check_update_time;
    $oauth_client_id = $settings->oauth_client_id;
    $oauth_client_secret = $settings->oauth_client_secret;
    $oauth_redirect_uri = $settings->oauth_redirect_uri;
    $oauth_access_token = $settings->oauth_access_token;
    $oauth_expires = $settings->oauth_expires;
    $oauth_refresh_token = $settings->oauth_refresh_token;
    $display_name = $settings->display_name;
    $userid = $settings->userid;
    $quick_mode = $settings->quick_mode;
    $output_application = $settings->output_application;
    $search_order = $settings->search_order;

    $search_categories = explode('▹', $search_order);

    foreach($search_categories as $search_category) {

        if($search_category == 'playlist') {

            // Search in Playlists

            $getPlaylists = 'select uri,name,nb_tracks,author,username,playlist_artwork_path,ownedbyuser,nb_playable_tracks,duration_playlist,collaborative,public from playlists where name like :query';

            try {
                $stmt = $db->prepare($getPlaylists);
                $stmt->bindValue(':query', '%'.$query.'%');
                $playlists = $stmt->execute();
            } catch (PDOException $e) {
                handleDbIssuePdoXml($db);

                return;
            }

            while ($playlist = $stmt->fetch()) {
                $added = ' ';
                $public_status = '';
                if (startswith($playlist[1], 'Artist radio for')) {
                    $added = '📻 ';
                }
                if ($playlist[9]) {
                    $public_status = 'collaborative';
                } else {
                    if ($playlist[10]) {
                        $public_status = 'public';
                    } else {
                        $public_status = 'private';
                    }
                }

                if ($quick_mode) {
                    if ($playlist[10]) {
                        $public_status_contrary = 'private';
                    } else {
                        $public_status_contrary = 'public';
                    }
                    $subtitle = '⚡️Launch Playlist';
                    $subtitle = $subtitle.' ,⇧ ▹ add playlist to ...,  ⌥ ▹ change playlist privacy to '.$public_status_contrary;
                    $added = ' ';
                    if ($userid == $playlist[4] && $public_status != 'collaborative') {
                        $cmdMsg = 'Change playlist privacy to '.$public_status_contrary;
                    } else {
                        $cmdMsg = 'Not Available';
                    }
                    if (startswith($playlist[1], 'Artist radio for')) {
                        $added = '📻 ';
                    }
                    $w->result(null, serialize(array(
                                '' /*track_uri*/,
                                '' /* album_uri */,
                                '' /* artist_uri */,
                                $playlist[0] /* playlist_uri */,
                                '' /* spotify_command */,
                                '' /* query */,
                                '' /* other_settings*/,
                                'set_playlist_privacy_to_'.$public_status_contrary /* other_action */,
                                '' /* artist_name */,
                                '' /* track_name */,
                                '' /* album_name */,
                                '' /* track_artwork_path */,
                                '' /* artist_artwork_path */,
                                '' /* album_artwork_path */,
                                $playlist[1] /* playlist_name */,
                                $playlist[5], /* playlist_artwork_path */
                            )), '🎵'.$added.$playlist[1].' by '.$playlist[3].' ● '.$playlist[7].' tracks ● '.$playlist[8], array(
                            $subtitle,
                            'alt' => 'Not Available',
                            'cmd' => $cmdMsg,
                            'shift' => 'Add playlist '.$playlist[1].' to ...',
                            'fn' => 'Not Available',
                            'ctrl' => 'Not Available',
                        ), $playlist[5], 'yes', null, '');
                } else {
                    $w->result(null, '', '🎵'.$added.$playlist[1], 'Browse '.$public_status.' playlist by '.$playlist[3].' ● '.$playlist[7].' tracks ● '.$playlist[8], $playlist[5], 'no', null, 'Playlist▹'.$playlist[0].'▹');
                }
            }
        }

        if($search_category == 'artist') {
            // Search artists

            if ($all_playlists == false) {
                $getTracks = "select artist_name,artist_uri,artist_artwork_path from tracks where yourmusic=1 and artist_uri!='' and artist_name like :artist_name limit ".$max_results;
            } else {
                $getTracks = "select artist_name,artist_uri,artist_artwork_path from tracks where artist_uri!='' and artist_name like :artist_name limit ".$max_results;
            }

            try {
                $stmt = $db->prepare($getTracks);
                $stmt->bindValue(':artist_name', '%'.$query.'%');
                $tracks = $stmt->execute();
            } catch (PDOException $e) {
                handleDbIssuePdoXml($db);

                return;
            }

            while ($track = $stmt->fetch()) {
                if (checkIfResultAlreadyThere($w->results(), '👤 '.$track[0]) == false) {
                    if ($quick_mode) {
                        $w->result(null, serialize(array(
                                    '' /*track_uri*/,
                                    '' /* album_uri */,
                                    $track[1] /* artist_uri */,
                                    '' /* playlist_uri */,
                                    '' /* spotify_command */,
                                    '' /* query */,
                                    '' /* other_settings*/,
                                    'playartist' /* other_action */,
                                    $track[0] /* artist_name */,
                                    '' /* track_name */,
                                    '' /* album_name */,
                                    '' /* track_artwork_path */,
                                    $track[0] /* artist_artwork_path */,
                                    '' /* album_artwork_path */,
                                    '' /* playlist_name */,
                                    '', /* playlist_artwork_path */
                                )), '👤 '.$track[0], '⚡️Play artist', $track[2], 'yes', null, '');
                    } else {
                        $w->result(null, '', '👤 '.$track[0], 'Browse this artist', $track[2], 'no', null, 'Artist▹'.$track[1].'∙'.$track[0].'▹');
                    }
                }
            }
        }

        if($search_category == 'track') {
            // Search tracks

            if ($all_playlists == false) {
                $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where yourmusic=1 and (artist_name like :query or album_name like :query or track_name like :query)'.'  order by added_at desc limit '.$max_results;
            } else {
                $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where (artist_name like :query or album_name like :query or track_name like :query)'.'  order by added_at desc limit '.$max_results;
            }

            try {
                $stmt = $db->prepare($getTracks);
                $stmt->bindValue(':query', '%'.$query.'%');
                $tracks = $stmt->execute();
            } catch (PDOException $e) {
                handleDbIssuePdoXml($db);

                return;
            }

            $noresult = true;
            $quick_mode_text = '';
            if ($quick_mode) {
                $quick_mode_text = '⚡️';
            }
            while ($track = $stmt->fetch()) {
                // if ($noresult) {
                //     $subtitle = "⌥ (play album) ⌘ (play artist) ctrl (lookup online)";
                //     $subtitle = "$subtitle fn (add track to ...) ⇧ (add album to ...)";
                //     $w->result(null, 'help', "Select a track below to play it (or choose alternative described below)", $subtitle, './images/info.png', 'no', null, '');
                // }
                $noresult = false;
                $subtitle = $track[6];
                $added = '';
                if ($track[18] == true) {
                    if ($output_application == 'MOPIDY') {
                        // skip local tracks if using Mopidy
                        continue;
                    }
                    $added = '📌 ';
                }
                if (checkIfResultAlreadyThere($w->results(), $added.$track[7].' ● '.$track[5]) == false) {
                    if ($track[14] == true) {
                        $w->result(null, serialize(array(
                                    $track[2] /*track_uri*/,
                                    $track[3] /* album_uri */,
                                    $track[4] /* artist_uri */,
                                    '' /* playlist_uri */,
                                    '' /* spotify_command */,
                                    '' /* query */,
                                    '' /* other_settings*/,
                                    '' /* other_action */,
                                    $track[7] /* artist_name */,
                                    $track[5] /* track_name */,
                                    $track[6] /* album_name */,
                                    $track[9] /* track_artwork_path */,
                                    $track[10] /* artist_artwork_path */,
                                    $track[11] /* album_artwork_path */,
                                    '' /* playlist_name */,
                                    '', /* playlist_artwork_path */
                                )), $added.$track[7].' ● '.$track[5], array(
                                $quick_mode_text.$track[16].' ● '.$subtitle.getPlaylistsForTrack($db, $track[2]),
                                'alt' => 'Play album '.$track[6].' in Spotify',
                                'cmd' => 'Play artist '.$track[7].' in Spotify',
                                'fn' => 'Add track '.$track[5].' to ...',
                                'shift' => 'Add album '.$track[6].' to ...',
                                'ctrl' => 'Search artist '.$track[7].' online',
                            ), $track[9], 'yes', array(
                                'copy' => $track[7].' ● '.$track[5],
                                'largetype' => $track[7].' ● '.$track[5],
                            ), '');
                    } else {
                        $w->result(null, '', '🚫 '.$track[7].' ● '.$track[5], $track[16].' ● '.$subtitle.getPlaylistsForTrack($db, $track[2]), $track[9], 'no', null, '');
                    }
                }
            }

            if ($noresult) {
                $w->result(null, 'help', 'There is no result for your search', '', './images/warning.png', 'no', null, '');
            }
        }

        if($search_category == 'album') {
            // Search albums

            if ($all_playlists == false) {
                $getTracks = 'select album_name,album_uri,album_artwork_path,uri from tracks where yourmusic=1 and album_name like :album_name group by album_name order by max(added_at) desc limit '.$max_results;
            } else {
                $getTracks = 'select album_name,album_uri,album_artwork_path,uri from tracks where album_name like :album_name group by album_name order by max(added_at) desc limit '.$max_results;
            }

            try {
                $stmt = $db->prepare($getTracks);
                $stmt->bindValue(':album_name', '%'.$query.'%');
                $tracks = $stmt->execute();
            } catch (PDOException $e) {
                handleDbIssuePdoXml($db);

                return;
            }

            while ($track = $stmt->fetch()) {
                if (checkIfResultAlreadyThere($w->results(), '💿 '.$track[0]) == false) {
                    if ($track[1] == '') {
                        // can happen for local tracks
                        $track[1] = $track[3];
                    }
                    if ($quick_mode) {
                        $w->result(null, serialize(array(
                                    '' /*track_uri*/,
                                    $track[1] /* album_uri */,
                                    '' /* artist_uri */,
                                    '' /* playlist_uri */,
                                    '' /* spotify_command */,
                                    '' /* query */,
                                    '' /* other_settings*/,
                                    'playalbum' /* other_action */,
                                    '' /* artist_name */,
                                    '' /* track_name */,
                                    $track[0] /* album_name */,
                                    '' /* track_artwork_path */,
                                    '' /* artist_artwork_path */,
                                    $track[2] /* album_artwork_path */,
                                    '' /* playlist_name */,
                                    '', /* playlist_artwork_path */
                                )), '💿 '.$track[0], '⚡️Play album', $track[2], 'yes', null, '');
                    } else {
                        $w->result(null, '', '💿 '.$track[0], 'Browse this album', $track[2], 'no', null, 'Album▹'.$track[1].'∙'.$track[0].'▹');
                    }
                }
            }
        }
    } // end foreach search_category


    if ($output_application != 'MOPIDY') {
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    $query /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    '' /* other_action */,

                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Search for '.$query.' in Spotify', array(
                'This will start a new search in Spotify',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
            ), './images/spotify.png', 'yes', null, '');
    }

    $w->result(null, null, 'Search for '.$query.' online', array(
            'This will search online, i.e not in your library',
            'alt' => 'Not Available',
            'cmd' => 'Not Available',
            'shift' => 'Not Available',
            'fn' => 'Not Available',
            'ctrl' => 'Not Available',
        ), './images/online.png', 'no', null, 'Search Online▹'.$query);
}

/**
 * searchCategoriesFastAccess function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function searchCategoriesFastAccess($w, $query, $settings, $db, $update_in_progress)
{
    $alfred_playlist_name = $settings->alfred_playlist_name;
    $now_playing_notifications = $settings->now_playing_notifications;

    // Search categories for fast access

    if (strpos(strtolower('playlists'), strtolower($query)) !== false) {
        $w->result(null, '', 'Playlists', 'Browse by playlist', './images/playlists.png', 'no', null, 'Playlist▹');
    }
    if (strpos(strtolower('albums'), strtolower($query)) !== false) {
        $w->result(null, '', 'Albums', 'Browse by album', './images/albums.png', 'no', null, 'Album▹');
    }
    if (strpos(strtolower('browse'), strtolower($query)) !== false) {
        $w->result(null, '', 'Browse', 'Browse Spotify by categories, as in the Spotify player’s “Browse” tab', './images/browse.png', 'no', null, 'Browse▹');
    }
    if (strpos(strtolower('your top'), strtolower($query)) !== false) {
        $w->result(null, '', 'Your Tops', 'Browse your top artists and top tracks', './images/star.png', 'no', null, 'Your Tops▹');
    }
    if (strpos(strtolower('recent'), strtolower($query)) !== false) {
        $w->result(null, '', 'Your Recent Tracks', 'Browse your recent tracks', './images/recent.png', 'no', null, 'Recent Tracks▹');
    }
    if (strpos(strtolower('lookup current artist online'), strtolower($query)) !== false) {
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'lookup_current_artist' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Lookup Current Artist online', array(
                '☁︎ Query all albums/tracks from current artist online..',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
            ), './images/online_artist.png', 'yes', '');
    }
    if (strpos(strtolower('search online'), strtolower($query)) !== false) {
        $w->result(null, '', 'Search online', '☁︎ You can search tracks, artists, albums and playlists online, i.e not in your library', './images/online.png', 'no', null, 'Search Online▹');
    }
    if (strpos(strtolower('new releases'), strtolower($query)) !== false) {
        $w->result(null, '', 'New Releases', 'Browse new album releases', './images/new_releases.png', 'no', null, 'New Releases▹');
    }
    if (strpos(strtolower('artists'), strtolower($query)) !== false) {
        $w->result(null, '', 'Artists', 'Browse by artist', './images/artists.png', 'no', null, 'Artist▹');
    }
    if (strpos(strtolower('play queue'), strtolower($query)) !== false) {
        if ($now_playing_notifications == true) {
            $w->result(null, '', 'Play Queue', 'Get the current play queue. Always use the workflow to launch tracks, otherwise play queue will be empty', './images/play_queue.png', 'no', null, 'Play Queue▹');
        }
    }
    if (strpos(strtolower('alfred'), strtolower($query)) !== false) {
        $w->result(null, '', 'Alfred Playlist (currently set to <'.$alfred_playlist_name.'>)', 'Choose one of your playlists and add tracks, album, playlist to it directly from the workflow', './images/alfred_playlist.png', 'no', null, 'Alfred Playlist▹Set Alfred Playlist▹');
    }
    if (strpos(strtolower('settings'), strtolower($query)) !== false) {
        $w->result(null, '', 'Settings', 'Go to settings', './images/settings.png', 'no', null, 'Settings▹');
    }
    if (strpos(strtolower('featured playlist'), strtolower($query)) !== false) {
        $w->result(null, '', 'Featured Playlist', 'Browse the current featured playlists', './images/star.png', 'no', null, 'Featured Playlist▹');
    }
    if (strpos(strtolower('your music'), strtolower($query)) !== false) {
        $w->result(null, '', 'Your Music', 'Browse Your Music', './images/tracks.png', 'no', null, 'Your Music▹');
    }
    if (strpos(strtolower('current track'), strtolower($query)) !== false) {
        $w->result(null, '', 'Current Track', 'Display current track information and browse various options', './images/current_track.png', 'no', null, 'Current Track▹');
    }
    if (strpos(strtolower('spotify connect'), strtolower($query)) !== false) {
        $w->result(null, '', 'Spotify Connect', 'Display Spotify Connect devices', './images/connect.png', 'no', null, 'Spotify Connect▹');
    }
}

/**
 * searchCommandsFastAccess function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function searchCommandsFastAccess($w, $query, $settings, $db, $update_in_progress)
{
    $all_playlists = $settings->all_playlists;
    $is_alfred_playlist_active = $settings->is_alfred_playlist_active;
    $radio_number_tracks = $settings->radio_number_tracks;
    $now_playing_notifications = $settings->now_playing_notifications;
    $max_results = $settings->max_results;
    $alfred_playlist_uri = $settings->alfred_playlist_uri;
    $alfred_playlist_name = $settings->alfred_playlist_name;
    $country_code = $settings->country_code;
    $last_check_update_time = $settings->last_check_update_time;
    $oauth_client_id = $settings->oauth_client_id;
    $oauth_client_secret = $settings->oauth_client_secret;
    $oauth_redirect_uri = $settings->oauth_redirect_uri;
    $oauth_access_token = $settings->oauth_access_token;
    $oauth_expires = $settings->oauth_expires;
    $oauth_refresh_token = $settings->oauth_refresh_token;
    $display_name = $settings->display_name;
    $userid = $settings->userid;

    $output_application = $settings->output_application;
    $mopidy_server = $settings->mopidy_server;
    $mopidy_port = $settings->mopidy_port;

    if (mb_strlen($query) < 2) {
        ////////

        // Fast Access to commands
        //////////////
        $w->result('SpotifyMiniPlayer_'.'next', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'next' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Next Track', 'Play the next track in Spotify', './images/next.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'previous', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'previous' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Previous Track', 'Play the previous track in Spotify', './images/previous.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'lookup_current_artist', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'lookup_current_artist' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Lookup Current Artist online', array(
                '☁︎ Query all albums/tracks from current artist online..',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
            ), './images/online_artist.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'lyrics', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'lyrics' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Get Lyrics for current track', array(
                'Get current track lyrics',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
            ), './images/lyrics.png', 'yes', '');


        $w->result('SpotifyMiniPlayer_'.'play', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'play' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Play', 'Play the current Spotify track', './images/play.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'play_current_artist', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'play_current_artist' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Play current artist', 'Play the current artist', './images/artists.png', 'yes', null, '');
        $w->result('SpotifyMiniPlayer_'.'play_current_album', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'play_current_album' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Play current album', 'Play the current album', './images/albums.png', 'yes', null, '');

        $w->result('SpotifyMiniPlayer_'.'pause', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'pause' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Pause', 'Pause the current Spotify track', './images/pause.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'playpause', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'playpause' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Play / Pause', 'Play or Pause the current Spotify track', './images/playpause.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'current', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'current' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Get Current Track info', 'Get current track information', './images/info.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'random', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'random' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Random Track', 'Play random track', './images/random.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'random_album', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'random_album' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Random Album', 'Play random album', './images/random_album.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'shuffle', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'shuffle' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Shuffle', 'Activate/Deactivate shuffling in Spotify', './images/shuffle.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'repeating', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'repeating' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Repeating', 'Activate/Deactivate repeating in Spotify', './images/repeating.png', 'yes', '');

            $w->result(null, serialize(array(
                '' /*track_uri*/,
                '' /* album_uri */,
                '' /* artist_uri */,
                '' /* playlist_uri */,
                '' /* spotify_command */,
                '' /* query */,
                '' /* other_settings*/,
                'share' /* other_action */,
                '' /* artist_name */,
                '' /* track_name */,
                '' /* album_name */,
                '' /* track_artwork_path */,
                '' /* artist_artwork_path */,
                '' /* album_artwork_path */,
                '' /* playlist_name */,
                '', /* playlist_artwork_path */
            )), 'Share current track using Mac OS X Sharing ', array(
            'This will open the Mac OS X Sharing for the current track',
            'alt' => 'Not Available',
            'cmd' => 'Not Available',
            'shift' => 'Not Available',
            'fn' => 'Not Available',
            'ctrl' => 'Not Available',
        ), './images/share.png', 'yes', null, '');

        $w->result(null, serialize(array(
            '' /*track_uri*/,
            '' /* album_uri */,
            '' /* artist_uri */,
            '' /* playlist_uri */,
            '' /* spotify_command */,
            '' /* query */,
            '' /* other_settings*/,
            'web_search' /* other_action */,
            '' /* artist_name */,
            '' /* track_name */,
            '' /* album_name */,
            '' /* track_artwork_path */,
            '' /* artist_artwork_path */,
            '' /* album_artwork_path */,
            '' /* playlist_name */,
            '', /* playlist_artwork_path */
        )), 'Do a web search for current track or artist on Youtube, Facebook, etc.. ', array(
        'You will be prompted to choose the web service you want to use',
        'alt' => 'Not Available',
        'cmd' => 'Not Available',
        'shift' => 'Not Available',
        'fn' => 'Not Available',
        'ctrl' => 'Not Available',
    ), './images/youtube.png', 'yes', null, '');

        if ($update_in_progress == false) {
            $w->result('SpotifyMiniPlayer_'.'refresh_library', serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'refresh_library' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Refresh your library', array(
                    'Do this when your library has changed (outside the scope of this workflow)',
                    'alt' => 'Not Available',
                    'cmd' => 'Not Available',
                    'shift' => 'Not Available',
                    'fn' => 'Not Available',
                    'ctrl' => 'Not Available',
                ), './images/update.png', 'yes', null, '');
        }

        if ($update_in_progress == false) {
            $w->result('SpotifyMiniPlayer_'.'current_artist_radio', serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'current_artist_radio' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Create artist radio playlist for current artist', 'Create artist radio playlist', './images/radio_artist.png', 'yes', '');

            $w->result('SpotifyMiniPlayer_'.'current_track_radio', serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'current_track_radio' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Create song radio playlist for current track', 'Create song radio playlist', './images/radio_song.png', 'yes', '');

            if ($is_alfred_playlist_active == true) {
                $w->result('SpotifyMiniPlayer_'.'add_current_track', serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'add_current_track' /* other_action */,

                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'Add current track to Alfred Playlist', 'Current track will be added to Alfred Playlist', './images/add_to_ap_yourmusic.png', 'yes', '');
            } else {
                $w->result('SpotifyMiniPlayer_'.'add_current_track', serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'add_current_track' /* other_action */,

                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'Add current track to Your Music', 'Current track will be added to Your Music', './images/add_to_ap_yourmusic.png', 'yes', '');
            }
            $w->result('SpotifyMiniPlayer_'.'add_current_track_to', serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'add_current_track_to' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Add current track to...', 'Current track will be added to Your Music or a playlist of your choice', './images/add_to.png', 'yes', '');

            $w->result('SpotifyMiniPlayer_'.'remove_current_track_from', serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'remove_current_track_from' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Remove current track from...', 'Current track will be removed from Your Music or a playlist of your choice', './images/remove_from.png', 'yes', '');
        }

        $w->result('SpotifyMiniPlayer_'.'mute', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'mute' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Mute/Unmute Spotify Volume', 'Mute/Unmute Volume', './images/mute.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'volume_down', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'volume_down' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Volume Down', 'Decrease Spotify Volume', './images/volume_down.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'volume_up', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'volume_up' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Volume Up', 'Increase Spotify Volume', './images/volume_up.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'volmax', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'volmax' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Set Spotify Volume to Maximum', 'Set the Spotify volume to maximum', './images/volmax.png', 'yes', '');

        $w->result('SpotifyMiniPlayer_'.'volmid', serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'volmid' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Set Spotify Volume to 50%', 'Set the Spotify volume to 50%', './images/volmid.png', 'yes', '');
    } else {

        // Search commands for fast access
        if (strpos(strtolower('share'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                '' /*track_uri*/,
                '' /* album_uri */,
                '' /* artist_uri */,
                '' /* playlist_uri */,
                '' /* spotify_command */,
                '' /* query */,
                '' /* other_settings*/,
                'share' /* other_action */,
                '' /* artist_name */,
                '' /* track_name */,
                '' /* album_name */,
                '' /* track_artwork_path */,
                '' /* artist_artwork_path */,
                '' /* album_artwork_path */,
                '' /* playlist_name */,
                '', /* playlist_artwork_path */
            )), 'Share current track using Mac OS X Sharing ', array(
            'This will open the Mac OS X Sharing for the current track',
            'alt' => 'Not Available',
            'cmd' => 'Not Available',
            'shift' => 'Not Available',
            'fn' => 'Not Available',
            'ctrl' => 'Not Available',
        ), './images/share.png', 'yes', null, '');
        }
        if (strpos(strtolower('web search'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                '' /*track_uri*/,
                '' /* album_uri */,
                '' /* artist_uri */,
                '' /* playlist_uri */,
                '' /* spotify_command */,
                '' /* query */,
                '' /* other_settings*/,
                'web_search' /* other_action */,
                '' /* artist_name */,
                '' /* track_name */,
                '' /* album_name */,
                '' /* track_artwork_path */,
                '' /* artist_artwork_path */,
                '' /* album_artwork_path */,
                '' /* playlist_name */,
                '', /* playlist_artwork_path */
            )), 'Do a web search for current track or artist on Youtube, Facebook, etc.. ', array(
            'You will be prompted to choose the web service you want to use',
            'alt' => 'Not Available',
            'cmd' => 'Not Available',
            'shift' => 'Not Available',
            'fn' => 'Not Available',
            'ctrl' => 'Not Available',
        ), './images/youtube.png', 'yes', null, '');
        }
        if (strpos(strtolower('next'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'next' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Next Track', 'Play the next track in Spotify', './images/next.png', 'yes', '');
        }
        if (strpos(strtolower('previous'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'previous' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Previous Track', 'Play the previous track in Spotify', './images/previous.png', 'yes', '');
        }
        if (strpos(strtolower('previous'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'previous' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Previous Track', 'Play the previous track in Spotify', './images/previous.png', 'yes', '');
        }
        if (strpos(strtolower('lyrics'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'lyrics' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Get Lyrics for current track', array(
                    'Get current track lyrics',
                    'alt' => 'Not Available',
                    'cmd' => 'Not Available',
                    'shift' => 'Not Available',
                    'fn' => 'Not Available',
                    'ctrl' => 'Not Available',
                ), './images/lyrics.png', 'yes', '');
        }

//		if (strpos(strtolower('biography'), strtolower($query)) !== false) {
//			$w->result('SpotifyMiniPlayer_' . 'biography', serialize(array(
//						'' /*track_uri*/ ,
//						'' /* album_uri */ ,
//						'' /* artist_uri */ ,
//						'' /* playlist_uri */ ,
//						'' /* spotify_command */ ,
//						'' /* query */ ,
//						'' /* other_settings*/ ,
//						'biography' /* other_action */ ,

//						'' /* artist_name */ ,
//						'' /* track_name */ ,
//						'' /* album_name */ ,
//						'' /* track_artwork_path */ ,
//						'' /* artist_artwork_path */ ,
//						'' /* album_artwork_path */ ,
//						'' /* playlist_name */ ,
//						'' /* playlist_artwork_path */
//					)), 'Display biography', array(
//					"This will display the artist biography, twitter and official website",
//					'alt' => 'Not Available',
//					'cmd' => 'Not Available',
//					'shift' => 'Not Available',
//					'fn' => 'Not Available',
//					'ctrl' => 'Not Available'
//				), './images/biography.png', 'yes', '');
//		}

        if (strpos(strtolower('query'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'lookup_current_artist' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Lookup Current Artist online', array(
                    '☁︎ Query all albums/tracks from current artist online..',
                    'alt' => 'Not Available',
                    'cmd' => 'Not Available',
                    'shift' => 'Not Available',
                    'fn' => 'Not Available',
                    'ctrl' => 'Not Available',
                ), './images/online_artist.png', 'yes', '');
        }
        if (strpos(strtolower('play'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'play' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Play', 'Play the current Spotify track', './images/play.png', 'yes', '');

            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'playpause' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Play / Pause', 'Play or Pause the current Spotify track', './images/playpause.png', 'yes', '');

            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'play_current_artist' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Play current artist', 'Play the current artist', './images/artists.png', 'yes', null, '');
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'play_current_album' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Play current album', 'Play the current album', './images/albums.png', 'yes', null, '');
        }
        if (strpos(strtolower('pause'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'pause' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Pause', 'Pause the current Spotify track', './images/pause.png', 'yes', '');

            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'playpause' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Play / Pause', 'Play or Pause the current Spotify track', './images/playpause.png', 'yes', '');
        }

        if (strpos(strtolower('current'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'current' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Get Current Track info', 'Get current track information', './images/info.png', 'yes', '');
        }
        if (strpos(strtolower('random'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'random' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Random Track', 'Play random track', './images/random.png', 'yes', '');

            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'random_album' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Random Album', 'Play random album', './images/random_album.png', 'yes', '');
        }
        if (strpos(strtolower('shuffle'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'shuffle' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Shuffle', 'Activate/Deactivate shuffling in Spotify', './images/shuffle.png', 'yes', '');
        }
        if (strpos(strtolower('repeating'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'repeating' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Repeating', 'Activate/Deactivate repeating in Spotify', './images/repeating.png', 'yes', '');
        }
        if (strpos(strtolower('refresh'), strtolower($query)) !== false) {
            if ($update_in_progress == false) {
                $w->result(null, serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'refresh_library' /* other_action */,

                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'Refresh your library', array(
                        'Do this when your library has changed (outside the scope of this workflow)',
                        'alt' => 'Not Available',
                        'cmd' => 'Not Available',
                        'shift' => 'Not Available',
                        'fn' => 'Not Available',
                        'ctrl' => 'Not Available',
                    ), './images/update.png', 'yes', null, '');
            }
        }
        if (strpos(strtolower('update'), strtolower($query)) !== false) {
            if ($update_in_progress == false) {
                $w->result(null, serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'refresh_library' /* other_action */,

                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'Refresh your library', array(
                        'Do this when your library has changed (outside the scope of this workflow)',
                        'alt' => 'Not Available',
                        'cmd' => 'Not Available',
                        'shift' => 'Not Available',
                        'fn' => 'Not Available',
                        'ctrl' => 'Not Available',
                    ), './images/update.png', 'yes', null, '');
            }
        }
        if ($update_in_progress == false) {
            if (strpos(strtolower('add'), strtolower($query)) !== false) {
                if ($is_alfred_playlist_active == true) {
                    $w->result(null, serialize(array(
                                '' /*track_uri*/,
                                '' /* album_uri */,
                                '' /* artist_uri */,
                                '' /* playlist_uri */,
                                '' /* spotify_command */,
                                '' /* query */,
                                '' /* other_settings*/,
                                'add_current_track' /* other_action */,

                                '' /* artist_name */,
                                '' /* track_name */,
                                '' /* album_name */,
                                '' /* track_artwork_path */,
                                '' /* artist_artwork_path */,
                                '' /* album_artwork_path */,
                                '' /* playlist_name */,
                                '', /* playlist_artwork_path */
                            )), 'Add current track to Alfred Playlist', 'Current track will be added to Alfred Playlist', './images/add_to_ap_yourmusic.png', 'yes', '');
                } else {
                    $w->result(null, serialize(array(
                                '' /*track_uri*/,
                                '' /* album_uri */,
                                '' /* artist_uri */,
                                '' /* playlist_uri */,
                                '' /* spotify_command */,
                                '' /* query */,
                                '' /* other_settings*/,
                                'add_current_track' /* other_action */,

                                '' /* artist_name */,
                                '' /* track_name */,
                                '' /* album_name */,
                                '' /* track_artwork_path */,
                                '' /* artist_artwork_path */,
                                '' /* album_artwork_path */,
                                '' /* playlist_name */,
                                '', /* playlist_artwork_path */
                            )), 'Add current track to Your Music', 'Current track will be added to Your Music', './images/add_to_ap_yourmusic.png', 'yes', '');
                }
                $w->result(null, serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'add_current_track_to' /* other_action */,

                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'Add current track to...', 'Current track will be added to Your Music or a playlist of your choice', './images/add_to.png', 'yes', '');
            }
            if (strpos(strtolower('remove'), strtolower($query)) !== false) {
                $w->result('SpotifyMiniPlayer_'.'remove_current_track_from', serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'remove_current_track_from' /* other_action */,

                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'Remove current track from...', 'Current track will be removed from Your Music or a playlist of your choice', './images/remove_from.png', 'yes', '');
            }
            if (strpos(strtolower('radio'), strtolower($query)) !== false) {
                $w->result('SpotifyMiniPlayer_'.'current_artist_radio', serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'current_artist_radio' /* other_action */,
                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'Create artist radio playlist for current artist', 'Create artist radio playlist', './images/radio_artist.png', 'yes', '');

                $w->result('SpotifyMiniPlayer_'.'current_track_radio', serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'current_track_radio' /* other_action */,
                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'Create song radio playlist for current track', 'Create song radio playlist', './images/radio_song.png', 'yes', '');
            }
        }
        if (strpos(strtolower('mute'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'mute' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Mute/Unmute Spotify Volume', 'Mute/Unmute Volume', './images/mute.png', 'yes', '');
        }
        if (strpos(strtolower('volume_down'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'volume_down' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Volume Down', 'Decrease Spotify Volume', './images/volume_down.png', 'yes', '');
        }
        if (strpos(strtolower('volume_up'), strtolower($query)) !== false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'volume_up' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Volume Up', 'Increase Spotify Volume', './images/volume_up.png', 'yes', '');
        }

        if (strpos(strtolower('volmax'), strtolower($query)) !== false) {
            $w->result('SpotifyMiniPlayer_'.'volmax', serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'volmax' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Set Spotify Volume to Maximum', 'Set the Spotify volume to maximum', './images/volmax.png', 'yes', '');
        }

        if (strpos(strtolower('volmid'), strtolower($query)) !== false) {
            $w->result('SpotifyMiniPlayer_'.'volmid', serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'volmid' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Set Spotify Volume to 50%', 'Set the Spotify volume to 50%', './images/volmid.png', 'yes', '');
        }
    }
}
