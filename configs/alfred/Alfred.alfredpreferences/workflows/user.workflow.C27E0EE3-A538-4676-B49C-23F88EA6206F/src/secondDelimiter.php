<?php

/**
 * secondDelimiterArtists function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterArtists($w, $query, $settings, $db, $update_in_progress)
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
    $is_public_playlists = $settings->is_public_playlists;
    $output_application = $settings->output_application;
    $use_artworks = $settings->use_artworks;

    // display tracks for selected artists

    $tmp = explode('∙', $words[1]);
    $artist_uri = $tmp[0];
    $artist_name = $tmp[1];
    $track = $words[2];

    $href = explode(':', $artist_uri);
    if ($href[1] == 'track') {
        $track_uri = $artist_uri;
        $artist_uri = getArtistUriFromTrack($w, $track_uri);
        if ($artist_uri == false) {
            $w->result(null, 'help', 'The artist cannot be retrieved from track uri', 'URI was '.$tmp[0], './images/warning.png', 'no', null, '');
            echo $w->tojson();

            exit;
        }
    }
    if ($href[1] == 'local') {
        $artist_uri = getArtistUriFromSearch($w, $href[2], $country_code);
        if ($artist_uri == false) {
            $w->result(null, 'help', 'The artist cannot be retrieved from local track uri', 'URI was '.$tmp[0], './images/warning.png', 'no', null, '');
            echo $w->tojson();

            exit;
        }
    }
    if (mb_strlen($track) < 2) {
        $artist_artwork_path = getArtistArtwork($w, $artist_uri, $artist_name, false, false, false, $use_artworks);
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    $artist_uri /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'playartist' /* other_action */,
                    $artist_name /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    $artist_artwork_path /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), '👤 '.$artist_name, 'Play artist', $artist_artwork_path, 'yes', null, '');
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    $artist_uri /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'lookup_artist' /* other_action */,
                    $artist_name /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), '👤 '.$artist_name, '☁︎ Query all albums/tracks from this artist online..', './images/online_artist.png', 'yes', null, '');

        //$w->result(null, '', "Display biography", "This will display the artist biography, twitter and official website", './images/biography.png', 'no', null, "Biography▹" . $artist_uri . '∙' . escapeQuery($artist_name) . '▹');

        $w->result(null, '', 'Follow/Unfollow Artist', 'Display options to follow/unfollow the artist', './images/follow.png', 'no', null, 'Follow/Unfollow▹'.$artist_uri.'@'.$artist_name.'▹');

        $w->result(null, '', 'Related Artists', 'Browse related artists', './images/related.png', 'no', null, 'OnlineRelated▹'.$artist_uri.'@'.$artist_name.'▹');

        if ($update_in_progress == false) {
            $privacy_status = 'private';
            if ($is_public_playlists) {
                $privacy_status = 'public';
            }
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        $artist_uri /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'radio_artist' /* other_action */,
                        $artist_name /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Create a Radio Playlist for '.$artist_name, 'This will create a '.$privacy_status.' radio playlist with '.$radio_number_tracks.' tracks for the artist', './images/radio_artist.png', 'yes', null, '');
        }

        if ($update_in_progress == false) {
            $privacy_status = 'private';
            if ($is_public_playlists) {
                $privacy_status = 'public';
            }
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        $artist_uri /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'complete_collection_artist' /* other_action */,
                        $artist_name /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Create a Complete Collection Playlist for '.$artist_name, 'This will create a '.$privacy_status.' playlist for the artist with all the albums and singles', './images/complete_collection.png', 'yes', null, '');
        }

        if ($all_playlists == false || count($tmp) == 3) {
            $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where yourmusic=1 and artist_uri=:artist_uri limit '.$max_results;
        } else {
            $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where artist_uri=:artist_uri limit '.$max_results;
        }
        $stmt = $db->prepare($getTracks);
        $stmt->bindValue(':artist_uri', $artist_uri);
    } else {
        if ($all_playlists == false || count($tmp) == 3) {
            $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where yourmusic=1 and (artist_uri=:artist_uri and track_name like :track)'.' limit '.$max_results;
        } else {
            $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where artist_uri=:artist_uri and track_name like :track limit '.$max_results;
        }
        $stmt = $db->prepare($getTracks);
        $stmt->bindValue(':artist_uri', $artist_uri);
        $stmt->bindValue(':track', '%'.$track.'%');
    }
    $tracks = $stmt->execute();
    $noresult = true;
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
                        $track[16].' ● '.$subtitle.getPlaylistsForTrack($db, $track[2]),
                        'alt' => 'Play album '.$track[6].' in Spotify',
                        'cmd' => 'Play artist '.$track[7].' in Spotify',
                        'fn' => 'Add track '.$track[5].' to ...',
                        'shift' => 'Add album '.$track[6].' to ...',
                        'ctrl' => 'Search artist '.$track[7].' online',
                    ), $track[9], 'yes', null, '');
            } else {
                $w->result(null, '', '🚫 '.$track[7].' ● '.$track[5], $track[16].' ● '.$subtitle.getPlaylistsForTrack($db, $track[2]), $track[9], 'no', null, '');
            }
        }
    }

    if ($noresult) {
        if (mb_strlen($track) < 2) {
            $w->result(null, 'help', 'There is no track in your library for the artist '.escapeQuery($artist_name), 'Choose one of the options above', './images/info.png', 'no', null, '');
        } else {
            $w->result(null, 'help', 'There is no result for your search', '', './images/warning.png', 'no', null, '');
        }
    }

    if ($output_application != 'MOPIDY') {
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    'artist:'.$artist_name /* spotify_command */,
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
                )), 'Search for artist '.$artist_name.' in Spotify', array(
                'This will start a new search in Spotify',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
            ), './images/spotify.png', 'yes', null, '');
    }
}

/**
 * secondDelimiterAlbums function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterAlbums($w, $query, $settings, $db, $update_in_progress)
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
    $output_application = $settings->output_application;
    $use_artworks = $settings->use_artworks;

    // display tracks for selected album

    $tmp = explode('∙', $words[1]);
    $album_uri = $tmp[0];
    $album_name = $tmp[1];
    $track = $words[2];

    $href = explode(':', $album_uri);
    if ($href[1] == 'track' || $href[1] == 'local') {
        $track_uri = $album_uri;
        $album_uri = getAlbumUriFromTrack($w, $track_uri);
        if ($album_uri == false) {
            $w->result(null, 'help', 'The album cannot be retrieved from track uri', 'URI was '.$tmp[0], './images/warning.png', 'no', null, '');
            echo $w->tojson();

            exit;
        }
    }

    try {
        if (mb_strlen($track) < 2) {
            if ($all_playlists == false || count($tmp) == 3) {
                $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where yourmusic=1 and album_uri=:album_uri limit '.$max_results;
            } else {
                $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where album_uri=:album_uri limit '.$max_results;
            }
            $stmt = $db->prepare($getTracks);
            $stmt->bindValue(':album_uri', $album_uri);
        } else {
            if ($all_playlists == false || count($tmp) == 3) {
                $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where yourmusic=1 and (album_uri=:album_uri and track_name like :track limit '.$max_results;
            } else {
                $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where album_uri=:album_uri and track_name like :track limit '.$max_results;
            }
            $stmt = $db->prepare($getTracks);
            $stmt->bindValue(':album_uri', $album_uri);
            $stmt->bindValue(':track', '%'.$track.'%');
        }

        $tracks = $stmt->execute();
    } catch (PDOException $e) {
        handleDbIssuePdoXml($db);

        exit;
    }

    $album_artwork_path = getTrackOrAlbumArtwork($w, $album_uri, false, false, false, $use_artworks);
    $w->result(null, serialize(array(
                '' /*track_uri*/,
                $album_uri /* album_uri */,
                '' /* artist_uri */,
                '' /* playlist_uri */,
                '' /* spotify_command */,
                '' /* query */,
                '' /* other_settings*/,
                'playalbum' /* other_action */,

                '' /* artist_name */,
                '' /* track_name */,
                $album_name /* album_name */,
                '' /* track_artwork_path */,
                '' /* artist_artwork_path */,
                $album_artwork_path /* album_artwork_path */,
                '' /* playlist_name */,
                '', /* playlist_artwork_path */
            )), '💿 '.$album_name, 'Play album', $album_artwork_path, 'yes', null, '');

    try {
        $getArtist = 'select artist_uri,artist_name from tracks where album_uri=:album_uri limit 1';
        $stmtGetArtist = $db->prepare($getArtist);
        $stmtGetArtist->bindValue(':album_uri', $album_uri);
        $tracks_artist = $stmtGetArtist->execute();
        $onetrack = $stmtGetArtist->fetch();
    } catch (PDOException $e) {
        handleDbIssuePdoXml($db);

        exit;
    }

    $w->result(null, '', '💿 '.$album_name, '☁︎ Query all tracks from this album online..', './images/online_album.png', 'no', null, 'Online▹'.$onetrack[0].'@'.$onetrack[1].'@'.$album_uri.'@'.$album_name.'▹');

    if ($update_in_progress == false) {
        $w->result(null, '', 'Add album '.escapeQuery($album_name).' to...', 'This will add the album to Your Music or a playlist you will choose in next step', './images/add.png', 'no', null, 'Add▹'.$album_uri.'∙'.escapeQuery($album_name).'▹');
    }
    $noresult = true;
    while ($track = $stmt->fetch()) {
        // if ($noresult == true) {
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
                            'play_track_in_album_context' /* other_action */,
                            $track[7] /* artist_name */,
                            $track[5] /* track_name */,
                            $track[6] /* album_name */,
                            $track[9] /* track_artwork_path */,
                            $track[10] /* artist_artwork_path */,
                            $track[11] /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), $added.$track[7].' ● '.$track[5], array(
                        $track[16].' ● '.$subtitle.getPlaylistsForTrack($db, $track[2]),
                        'alt' => 'Play album '.$track[6].' in Spotify',
                        'cmd' => 'Play artist '.$track[7].' in Spotify',
                        'fn' => 'Add track '.$track[5].' to ...',
                        'shift' => 'Add album '.$track[6].' to ...',
                        'ctrl' => 'Search artist '.$track[7].' online',
                    ), $track[9], 'yes', null, '');
            } else {
                $w->result(null, '', '🚫 '.$track[7].' ● '.$track[5], $track[16].' ● '.$subtitle.getPlaylistsForTrack($db, $track[2]), $track[9], 'no', null, '');
            }
        }
    }

    if ($noresult) {
        $w->result(null, 'help', 'There is no result for your search', '', './images/warning.png', 'no', null, '');
    }

    if ($output_application != 'MOPIDY') {
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    'album:'.$album_name /* spotify_command */,
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
                )), 'Search for album '.$album_name.' in Spotify', array(
                'This will start a new search in Spotify',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
            ), './images/spotify.png', 'yes', null, '');
    }
}

/**
 * secondDelimiterPlaylists function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterPlaylists($w, $query, $settings, $db, $update_in_progress)
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

    $output_application = $settings->output_application;

    // display tracks for selected playlist

    $theplaylisturi = $words[1];
    $thetrack = $words[2];
    $getPlaylists = 'select uri,name,nb_tracks,author,username,playlist_artwork_path,ownedbyuser,nb_playable_tracks,duration_playlist,collaborative,public from playlists where uri=:uri';

    try {
        $stmt = $db->prepare($getPlaylists);
        $stmt->bindValue(':uri', $theplaylisturi);

        $playlists = $stmt->execute();
        $noresultplaylist = true;
        while ($playlist = $stmt->fetch()) {
            $noresultplaylist = false;
            if (mb_strlen($thetrack) < 2) {
                if ($playlist[9]) {
                    $public_status = 'collaborative';
                } else {
                    if ($playlist[10]) {
                        $public_status = 'public';
                    } else {
                        $public_status = 'private';
                    }
                }
                if ($playlist[10]) {
                    $public_status_contrary = 'private';
                } else {
                    $public_status_contrary = 'public';
                }
                $subtitle = 'Launch Playlist';
                $subtitle = $subtitle.' ,⇧ ▹ add playlist to ..., ⌘ ▹ change playlist privacy to '.$public_status_contrary;
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
                if ($output_application != 'MOPIDY') {
                    $w->result(null, serialize(array(
                                '' /*track_uri*/,
                                '' /* album_uri */,
                                '' /* artist_uri */,
                                '' /* playlist_uri */,
                                'activate (open location "'.$playlist[0].'")' /* spotify_command */,
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
                            )), 'Open playlist '.escapeQuery($playlist[1]).' in Spotify', 'This will open the playlist in Spotify', './images/spotify.png', 'yes', null, '');
                }

                if ($update_in_progress == false) {
                    $w->result(null, '', 'Add playlist '.escapeQuery($playlist[1]).' to...', 'This will add the playlist to Your Music or a playlist you will choose in next step', './images/add.png', 'no', null, 'Add▹'.$playlist[0].'∙'.escapeQuery($playlist[1]).'▹');
                }

                if ($update_in_progress == false) {
                    $w->result(null, '', 'Remove playlist '.escapeQuery($playlist[1]), 'A confirmation will be asked in next step', './images/uncheck.png', 'no', null, 'Confirm Remove Playlist▹'.$playlist[0].'∙'.escapeQuery($playlist[1]).'▹');
                }
                $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where playlist_uri=:theplaylisturi order by added_at desc limit '.$max_results;
                $stmt = $db->prepare($getTracks);
                $stmt->bindValue(':theplaylisturi', $theplaylisturi);
            } else {
                $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where playlist_uri=:theplaylisturi and (artist_name like :track or album_name like :track or track_name like :track)'.' order by added_at desc limit '.$max_results;
                $stmt = $db->prepare($getTracks);
                $stmt->bindValue(':theplaylisturi', $theplaylisturi);
                $stmt->bindValue(':track', '%'.$thetrack.'%');
            }

            if($theplaylisturi == $alfred_playlist_uri) {
                if ($update_in_progress == false) {
                    $w->result(null, '', 'Change your Alfred playlist', 'Select one of your playlists as your Alfred playlist', './images/settings.png', 'no', null, 'Alfred Playlist▹Set Alfred Playlist▹');
                }
            }

            $tracks = $stmt->execute();
            $noresult = true;
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
                                    $theplaylisturi /* playlist_uri */,
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
                                    $playlist[1] /* playlist_name */,
                                    '', /* playlist_artwork_path */
                                )), $added.$track[7].' ● '.$track[5], array(
                                $track[16].' ● '.$subtitle.getPlaylistsForTrack($db, $track[2]),
                                'alt' => 'Play album '.$track[6].' in Spotify',
                                'cmd' => 'Play artist '.$track[7].' in Spotify',
                                'fn' => 'Add track '.$track[5].' to ...',
                                'shift' => 'Add album '.$track[6].' to ...',
                                'ctrl' => 'Search artist '.$track[7].' online',
                            ), $track[9], 'yes', null, '');
                    } else {
                        $w->result(null, '', '🚫 '.$track[7].' ● '.$track[5], $track[16].' ● '.$subtitle.getPlaylistsForTrack($db, $track[2]), $track[9], 'no', null, '');
                    }
                }
            }

            if ($noresult) {
                $w->result(null, 'help', 'There is no result for your search', '', './images/warning.png', 'no', null, '');
            }
        }

        if($theplaylisturi == $alfred_playlist_uri) {
            if ($update_in_progress == false) {
                if (strtolower($r[3]) != strtolower('Starred')) {
                    $w->result(null, '', 'Clear your Alfred Playlist', 'This will remove all the tracks in your current Alfred Playlist', './images/uncheck.png', 'no', null, 'Alfred Playlist▹Confirm Clear Alfred Playlist▹');
                }
            }
        }

        // can happen only with Alfred Playlist deleted
        if ($noresultplaylist) {
            $w->result(null, 'help', 'It seems your Alfred Playlist was deleted', 'Choose option below to change it', './images/warning.png', 'no', null, '');
            $w->result(null, '', 'Change your Alfred playlist', 'Select one of your playlists below as your Alfred playlist', './images/settings.png', 'no', null, 'Alfred Playlist▹Set Alfred Playlist▹');
        }
    } catch (PDOException $e) {
        handleDbIssuePdoXml($db);

        exit;
    }
}

/**
 * secondDelimiterOnline function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterOnline($w, $query, $settings, $db, $update_in_progress)
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
    $use_artworks = $settings->use_artworks;

    if (substr_count($query, '@') == 1) {

        // Search Artist Online

        $tmp = $words[1];
        $words = explode('@', $tmp);
        $artist_uri = $words[0];
        $tmp_uri = explode(':', $artist_uri);

        $artist_name = $words[1];

        $artist_artwork_path = getArtistArtwork($w, $artist_uri, $artist_name, false, false, false, $use_artworks);
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    $artist_uri /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'playartist' /* other_action */,
                    $artist_name /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    $artist_artwork_path /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), '👤 '.escapeQuery($artist_name), 'Play artist', $artist_artwork_path, 'yes', null, '');

        //$w->result(null, '', "Display biography", "This will display the artist biography, twitter and official website", './images/biography.png', 'no', null, "Biography▹" . $artist_uri . '∙' . escapeQuery($artist_name) . '▹');

        $w->result(null, '', 'Follow/Unfollow Artist', 'Display options to follow/unfollow the artist', './images/follow.png', 'no', null, 'Follow/Unfollow▹'.$artist_uri.'@'.$artist_name.'▹');

        $w->result(null, '', 'Related Artists', 'Browse related artists', './images/related.png', 'no', null, 'OnlineRelated▹'.$artist_uri.'@'.$artist_name.'▹');

        if ($update_in_progress == false) {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        $artist_uri /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'radio_artist' /* other_action */,
                        $artist_name /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Create a Radio Playlist for '.$artist_name, 'This will create a radio playlist with '.$radio_number_tracks.' tracks for the artist', './images/radio_artist.png', 'yes', null, '');
        }

        // call to web api, if it fails,
        // it displays an error in main window
        $albums = getTheArtistAlbums($w, $artist_uri, $country_code);

        $w->result(null, 'help', 'Select an album below to browse it', 'singles and compilations are also displayed', './images/info.png', 'no', null, '');

        $noresult = true;
        foreach ($albums as $album) {
            if (checkIfResultAlreadyThere($w->results(), $album->name.' ('.count($album->tracks->items).' tracks)') == false) {
                $noresult = false;
                $genre = (count($album->genres) > 0) ? ' ● Genre: '.implode('|', $album->genres) : '';
                $tracks = $album->tracks;
                $w->result(null, '', $album->name.' ('.count($album->tracks->items).' tracks)', $album->album_type.' by '.$artist_name.' ● Release date: '.$album->release_date.$genre, getTrackOrAlbumArtwork($w, $album->uri, false, false, false, $use_artworks), 'no', null, 'Online▹'.$artist_uri.'@'.$artist_name.'@'.$album->uri.'@'.$album->name.'▹');
            }
        }

        if ($noresult) {
            $w->result(null, 'help', 'There is no album for this artist', '', './images/warning.png', 'no', null, '');
        }
    } elseif (substr_count($query, '@') == 3) {

        // Search Album Online

        $tmp = $words[1];
        $words = explode('@', $tmp);
        $artist_uri = $words[0];
        $artist_name = $words[1];
        $album_uri = $words[2];
        $album_name = $words[3];

        $href = explode(':', $album_uri);
        if ($href[1] == 'track' || $href[1] == 'local') {
            $track_uri = $album_uri;
            $album_uri = getAlbumUriFromTrack($w, $track_uri);
            if ($album_uri == false) {
                $w->result(null, 'help', 'The album cannot be retrieved from track uri', 'URI was '.$track_uri, './images/warning.png', 'no', null, '');
                echo $w->tojson();
                exit;
            }
        }
        $href = explode(':', $artist_uri);
        if ($href[1] == 'track') {
            $track_uri = $artist_uri;
            $artist_uri = getArtistUriFromTrack($w, $track_uri);
            if ($artist_uri == false) {
                $w->result(null, 'help', 'The artist cannot be retrieved from track uri', 'URI was '.$track_uri, './images/warning.png', 'no', null, '');
                echo $w->tojson();
                exit;
            }
        }
        $album_artwork_path = getTrackOrAlbumArtwork($w, $album_uri, false, false, false, $use_artworks);
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    $album_uri /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'playalbum' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    $album_name /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    $album_artwork_path /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), '💿 '.escapeQuery($album_name), 'Play album', $album_artwork_path, 'yes', null, '');

        if ($update_in_progress == false) {
            $w->result(null, '', 'Add album '.escapeQuery($album_name).' to...', 'This will add the album to Your Music or a playlist you will choose in next step', './images/add.png', 'no', null, 'Add▹'.$album_uri.'∙'.escapeQuery($album_name).'▹');
        }

        // call to web api, if it fails,
        // it displays an error in main window
        $tracks = getTheAlbumFullTracks($w, $album_uri);
        $noresult = true;
        foreach ($tracks as $track) {
            // if ($noresult == true) {
            //     $subtitle = "⌥ (play album) ⌘ (play artist) ctrl (lookup online)";
            //     $subtitle = "$subtitle fn (add track to ...) ⇧ (add album to ...)";
            //     $w->result(null, 'help', "Select a track below to play it (or choose alternative described below)", $subtitle, './images/info.png', 'no', null, '');
            // }
            $track_artwork = getTrackOrAlbumArtwork($w, $track->uri, false, false, false, $use_artworks);
            if (isset($track->is_playable) && $track->is_playable) {
                $noresult = false;
                $w->result(null, serialize(array(
                            $track->uri /*track_uri*/,
                            $album_uri /* album_uri */,
                            $artist_uri /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'play_track_in_album_context' /* other_action */,
                            $artist_name /* artist_name */,
                            $track->name /* track_name */,
                            $album_name /* album_name */,
                            $track_artwork /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), escapeQuery($artist_name).' ● '.escapeQuery($track->name), array(
                        beautifyTime($track->duration_ms / 1000).' ● '.$album_name,
                        'alt' => 'Play album '.escapeQuery($album_name).' in Spotify',
                        'cmd' => 'Play artist '.escapeQuery($artist_name).' in Spotify',
                        'fn' => 'Add track '.escapeQuery($track->name).' to ...',
                        'shift' => 'Add album '.escapeQuery($album_name).' to ...',
                        'ctrl' => 'Search artist '.escapeQuery($artist_name).' online',
                    ), $track_artwork, 'yes', null, '');
            } else {
                $w->result(null, '', '🚫 '.escapeQuery($artist_name).' ● '.escapeQuery($track->name), beautifyTime($track->duration_ms / 1000).' ● '.$album_name, $track_artwork, 'no', null, '');
            }
        }
    }
}

/**
 * secondDelimiterOnlineRelated function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterOnlineRelated($w, $query, $settings, $db, $update_in_progress)
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

    if (substr_count($query, '@') == 1) {

        // Search Related Artist Online

        $tmp = $words[1];
        $words = explode('@', $tmp);
        $artist_uri = $words[0];
        $artist_name = $words[1];

        // call to web api, if it fails,
        // it displays an error in main window
        $relateds = getTheArtistRelatedArtists($w, trim($artist_uri));

        foreach ($relateds as $related) {
            $w->result(null, '', '👤 '.$related->name, '☁︎ Query all albums/tracks from this artist online..', getArtistArtwork($w, $related->uri, $related->name, false, false, false, $use_artworks), 'no', null, 'Online▹'.$related->uri.'@'.$related->name.'▹');
        }
    }
}

/**
 * secondDelimiterOnlinePlaylist function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterOnlinePlaylist($w, $query, $settings, $db, $update_in_progress)
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
    $is_public_playlists = $settings->is_public_playlists;
    $output_application = $settings->output_application;
    $use_artworks = $settings->use_artworks;

    // display tracks for selected online playlist

    $tmp = explode('∙', $words[1]);
    $theplaylisturi = $tmp[0];
    $url = explode(':', $theplaylisturi);
    $owner_id = $url[2];
    $playlist_id = $url[4];

    $theplaylistname = $tmp[1];
    $thetrack = $words[2];
    $savedPlaylistTracks = array();
    $duration_playlist = 0;
    $nb_tracks = 0;
    try {
        $api = getSpotifyWebAPI($w);
        $offsetGetUserPlaylistTracks = 0;
        $limitGetUserPlaylistTracks = 100;
        do {
            // refresh api
            $api = getSpotifyWebAPI($w, false, $api);
            $userPlaylistTracks = $api->getUserPlaylistTracks($owner_id, $playlist_id, array(
                    'fields' => array(
                        'total',
                        'items(added_at)',
                        'items(is_local)',
                        'items.track(is_playable,duration_ms,uri,popularity,name)',
                        'items.track.album(album_type,images,uri,name)',
                        'items.track.artists(name,uri)',
                    ),
                    'limit' => $limitGetUserPlaylistTracks,
                    'offset' => $offsetGetUserPlaylistTracks,
                    'market' => $country_code,
                ));

            foreach ($userPlaylistTracks->items as $item) {
                $track = $item->track;
                $savedPlaylistTracks[] = $item;
                $nb_tracks += 1;
                $duration_playlist += $track->duration_ms;
            }
            $offsetGetUserPlaylistTracks += $limitGetUserPlaylistTracks;
        } while ($offsetGetUserPlaylistTracks < $userPlaylistTracks->total);
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        $w->result(null, 'help', 'Exception occurred', ''.$e->getMessage(), './images/warning.png', 'no', null, '');
        echo $w->tojson();
        exit;
    }

    $subtitle = 'Launch Playlist';
    if ($is_alfred_playlist_active == true) {
        $subtitle = "$subtitle ,⇧ ▹ add playlist to ...";
    }
    $playlist_artwork_path = getPlaylistArtwork($w, $theplaylisturi, false, false, $use_artworks);
    $w->result(null, serialize(array(
                '' /*track_uri*/,
                '' /* album_uri */,
                '' /* artist_uri */,
                $theplaylisturi /* playlist_uri */,
                '' /* spotify_command */,
                '' /* query */,
                '' /* other_settings*/,
                '' /* other_action */,

                '' /* artist_name */,
                '' /* track_name */,
                '' /* album_name */,
                '' /* track_artwork_path */,
                '' /* artist_artwork_path */,
                '' /* album_artwork_path */,
                $theplaylistname /* playlist_name */,
                $playlist_artwork_path /* playlist_artwork_path */,
                $alfred_playlist_name,
                /* alfred_playlist_name */
            )), '🎵'.$theplaylistname.' by '.$owner_id.' ● '.$nb_tracks.' tracks ● '.beautifyTime($duration_playlist / 1000, true), array(
            $subtitle,
            'alt' => 'Not Available',
            'cmd' => 'Not Available',
            'shift' => 'Add playlist '.$theplaylistname.' to your Alfred Playlist',
            'fn' => 'Not Available',
            'ctrl' => 'Not Available',
        ), $playlist_artwork_path, 'yes', null, '');

    if ($output_application != 'MOPIDY') {
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    'activate (open location "'.$theplaylisturi.'")' /* spotify_command */,
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
                )), 'Open playlist '.$theplaylistname.' in Spotify', 'This will open the playlist in Spotify', './images/spotify.png', 'yes', null, '');
    }
    if ($update_in_progress == false) {
        $added = 'privately';
        $privacy_status = 'private';
        if ($is_public_playlists) {
            $added = 'publicly';
            $privacy_status = 'public';
        }
        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    $theplaylisturi /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'follow_playlist' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    $theplaylistname /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Follow '.$added.' playlist '.$theplaylistname, 'This will add the playlist (marked as '.$privacy_status.') to your library', './images/follow.png', 'yes', null, '');
    }

    $noresult = true;
    $nb_results = 0;
    foreach ($savedPlaylistTracks as $item) {
        if ($nb_results > $max_results) {
            break;
        }
        $track = $item->track;
        // if ($noresult) {
        //     $subtitle = "⌥ (play album) ⌘ (play artist) ctrl (lookup online)";
        //     $subtitle = "$subtitle fn (add track to ...) ⇧ (add album to ...)";
        //     $w->result(null, 'help', "Select a track below to play it (or choose alternative described below)", $subtitle, './images/info.png', 'no', null, '');
        // }
        $noresult = false;
        $artists = $track->artists;
        $artist = $artists[0];
        $album = $track->album;

        $track_artwork_path = getTrackOrAlbumArtwork($w, $track->uri, false, false, false, $use_artworks);
        if (isset($track->is_playable) && $track->is_playable) {
            $w->result(null, serialize(array(
                        $track->uri /*track_uri*/,
                        $album->uri /* album_uri */,
                        $artist->uri /* artist_uri */,
                        $theplaylisturi /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        '' /* other_action */,
                        escapeQuery($artist->name) /* artist_name */,
                        escapeQuery($track->name) /* track_name */,
                        escapeQuery($album->name) /* album_name */,
                        $track_artwork_path /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), escapeQuery($artist->name).' ● '.escapeQuery($track->name), array(
                    beautifyTime($track->duration_ms / 1000).' ● '.escapeQuery($album->name),
                    'alt' => 'Play album '.escapeQuery($album->name).' in Spotify',
                    'cmd' => 'Play artist '.escapeQuery($artist->name).' in Spotify',
                    'fn' => 'Add track '.escapeQuery($track->name).' to ...',
                    'shift' => 'Add album '.escapeQuery($album->name).' to ...',
                    'ctrl' => 'Search artist '.escapeQuery($artist->name).' online',
                ), $track_artwork_path, 'yes', null, '');
            ++$nb_results;
        } else {
            $added = '';
            if (isset($item->is_local) && $item->is_local) {
                $added = '📌 ';
            } else {
                $added = '🚫 ';
            }
            $w->result(null, '', $added.escapeQuery($artist->name).' ● '.escapeQuery($track->name), beautifyTime($track->duration_ms / 1000).' ● '.escapeQuery($album->name), $track_artwork_path, 'no', null, '');
            ++$nb_results;
        }
    }
}

/**
 * secondDelimiterYourMusicTracks function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterYourMusicTracks($w, $query, $settings, $db, $update_in_progress)
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

    $output_application = $settings->output_application;

    // display tracks for Your Music

    $thetrack = $words[2];

    if (mb_strlen($thetrack) < 2) {
        $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where yourmusic=1 order by added_at desc limit '.$max_results;
        $stmt = $db->prepare($getTracks);
    } else {
        $getTracks = 'select yourmusic, popularity, uri, album_uri, artist_uri, track_name, album_name, artist_name, album_type, track_artwork_path, artist_artwork_path, album_artwork_path, playlist_name, playlist_uri, playable, added_at, duration, nb_times_played, local_track from tracks where yourmusic=1 and (artist_name like :track or album_name like :track or track_name like :track)'.' order by added_at desc limit '.$max_results;
        $stmt = $db->prepare($getTracks);
        $stmt->bindValue(':track', '%'.$thetrack.'%');
    }

    $tracks = $stmt->execute();

    $noresult = true;
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
                        $track[16].' ● '.$subtitle.getPlaylistsForTrack($db, $track[2]),
                        'alt' => 'Play album '.$track[6].' in Spotify',
                        'cmd' => 'Play artist '.$track[7].' in Spotify',
                        'fn' => 'Add track '.$track[5].' to ...',
                        'shift' => 'Add album '.$track[6].' to ...',
                        'ctrl' => 'Search artist '.$track[7].' online',
                    ), $track[9], 'yes', null, '');
            } else {
                $w->result(null, '', '🚫 '.$track[7].' ● '.$track[5], $track[16].' ● '.$subtitle.getPlaylistsForTrack($db, $track[2]), $track[9], 'no', null, '');
            }
        }
    }

    if ($noresult) {
        $w->result(null, 'help', 'There is no result for your search', '', './images/warning.png', 'no', null, '');
    }

    if (mb_strlen($thetrack) > 0) {
        if ($output_application != 'MOPIDY') {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        $thetrack  /* spotify_command */,
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
                    )), 'Search for '.$thetrack.' in Spotify', array(
                    'This will start a new search in Spotify',
                    'alt' => 'Not Available',
                    'cmd' => 'Not Available',
                    'shift' => 'Not Available',
                    'fn' => 'Not Available',
                    'ctrl' => 'Not Available',
                ), './images/spotify.png', 'yes', null, '');
        }

        $w->result(null, null, 'Search for '.$thetrack.' online', array(
                'This will search online, i.e not in your library',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
            ), './images/online.png', 'no', null, 'Search Online▹'.$thetrack);
    }
}

/**
 * secondDelimiterYourMusicAlbums function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterYourMusicAlbums($w, $query, $settings, $db, $update_in_progress)
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

    // Search albums

    $album = $words[2];
    try {
        if (mb_strlen($album) < 2) {
            $getTracks = 'select album_name,album_artwork_path,artist_name,album_uri,album_type from tracks where yourmusic=1'.' group by album_name order by max(added_at) desc limit '.$max_results;
            $stmt = $db->prepare($getTracks);
        } else {
            $getTracks = 'select album_name,album_artwork_path,artist_name,album_uri,album_type from tracks where yourmusic=1 and album_name like :query group by album_name order by max(added_at) desc limit '.$max_results;
            $stmt = $db->prepare($getTracks);
            $stmt->bindValue(':query', '%'.$album.'%');
        }

        $tracks = $stmt->execute();
    } catch (PDOException $e) {
        handleDbIssuePdoXml($db);

        exit;
    }

    // display all albums
    $noresult = true;
    while ($track = $stmt->fetch()) {
        $noresult = false;
        $nb_album_tracks = getNumberOfTracksForAlbum($db, $track[3], true);
        if (checkIfResultAlreadyThere($w->results(), $track[0].' ('.$nb_album_tracks.' tracks)') == false) {
            $w->result(null, '', $track[0].' ('.$nb_album_tracks.' tracks)', $track[4].' by '.$track[2], $track[1], 'no', null, 'Album▹'.$track[3].'∙'.$track[0].'∙'.' ★ '.'▹');
        }
    }

    if ($noresult) {
        $w->result(null, 'help', 'There is no result for your search', '', './images/warning.png', 'no', null, '');
    }
}

/**
 * secondDelimiterYourTopArtists function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterYourTopArtists($w, $query, $settings, $db, $update_in_progress)
{
    $words = explode('▹', $query);
    $kind = $words[0];
    $time_range = $words[2];

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

    try {
        $api = getSpotifyWebAPI($w);
        $topArtists = $api->getMyTop('artists', array(
                'time_range' => $time_range,
                'limit' => ($max_results <= 50) ? $max_results : 50,
            ));

        $items = $topArtists->items;
        $noresult = true;
        foreach ($items as $artist) {
            $noresult = false;
            $w->result(null, '', '👤 '.$artist->name, 'Browse this artist', getArtistArtwork($w, $artist->uri, $artist->name, false, false, false, $use_artworks), 'no', null, 'Artist▹'.$artist->uri.'∙'.$artist->name.'▹');
        }

        if ($noresult) {
            $w->result(null, 'help', 'There is no result for your top artists', '', './images/warning.png', 'no', null, '');
        }
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        $w->result(null, 'help', 'Exception occurred', ''.$e->getMessage(), './images/warning.png', 'no', null, '');
        echo $w->tojson();
        exit;
    }
}

/**
 * secondDelimiterYourTopTracks function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterYourTopTracks($w, $query, $settings, $db, $update_in_progress)
{
    $words = explode('▹', $query);
    $kind = $words[0];
    $time_range = $words[2];

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

    try {
        $api = getSpotifyWebAPI($w);
        $topTracks = $api->getMyTop('tracks', array(
                'time_range' => $time_range,
                'limit' => ($max_results <= 50) ? $max_results : 50,
            ));

        $noresult = true;

        $items = $topTracks->items;

        foreach ($items as $track) {
            // if ($noresult) {
            //     $subtitle = "⌥ (play album) ⌘ (play artist) ctrl (lookup online)";
            //     $subtitle = "$subtitle fn (add track to ...) ⇧ (add album to ...)";
            //     $w->result(null, 'help', "Select a track below to play it (or choose alternative described below)", $subtitle, './images/info.png', 'no', null, '');
            // }
            $noresult = false;
            $artists = $track->artists;
            $artist = $artists[0];
            $album = $track->album;

            $track_artwork_path = getTrackOrAlbumArtwork($w, $track->uri, false, false, false, $use_artworks);
            if (isset($track->is_playable) && $track->is_playable) {
                $w->result(null, serialize(array(
                            $track->uri /*track_uri*/,
                            $album->uri /* album_uri */,
                            $artist->uri /* artist_uri */,
                            $theplaylisturi /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            '' /* other_action */,
                            escapeQuery($artist->name) /* artist_name */,
                            escapeQuery($track->name) /* track_name */,
                            escapeQuery($album->name) /* album_name */,
                            $track_artwork_path /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), escapeQuery($artist->name).' ● '.escapeQuery($track->name), array(
                        beautifyTime($track->duration_ms / 1000).' ● '.escapeQuery($album->name),
                        'alt' => 'Play album '.escapeQuery($album->name).' in Spotify',
                        'cmd' => 'Play artist '.escapeQuery($artist->name).' in Spotify',
                        'fn' => 'Add track '.escapeQuery($track->name).' to ...',
                        'shift' => 'Add album '.escapeQuery($album->name).' to ...',
                        'ctrl' => 'Search artist '.escapeQuery($artist->name).' online',
                    ), $track_artwork_path, 'yes', null, '');
                ++$nb_results;
            } else {
                $added = '';
                if (isset($item->is_local) && $item->is_local) {
                    $added = '📌 ';
                } else {
                    $added = '🚫 ';
                }
                $w->result(null, '', $added.escapeQuery($artist->name).' ● '.escapeQuery($track->name), beautifyTime($track->duration_ms / 1000).' ● '.escapeQuery($album->name), $track_artwork_path, 'no', null, '');
                ++$nb_results;
            }
        }

        if ($noresult) {
            $w->result(null, 'help', 'There is no result for your top tracks', '', './images/warning.png', 'no', null, '');
        }
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
        $w->result(null, 'help', 'Exception occurred', ''.$e->getMessage(), './images/warning.png', 'no', null, '');
        echo $w->tojson();
        exit;
    }
}

/**
 * secondDelimiterYourMusicArtists function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterYourMusicArtists($w, $query, $settings, $db, $update_in_progress)
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

    // Search artists

    $artist = $words[2];

    try {
        if (mb_strlen($artist) < 2) {
            $getTracks = 'select artist_name,artist_artwork_path,artist_uri from tracks where yourmusic=1 group by artist_name'.' limit '.$max_results;
            $stmt = $db->prepare($getTracks);
        } else {
            $getTracks = 'select artist_name,artist_artwork_path,artist_uri from tracks where yourmusic=1 and artist_name like :query limit '.$max_results;
            $stmt = $db->prepare($getTracks);
            $stmt->bindValue(':query', '%'.$artist.'%');
        }

        $tracks = $stmt->execute();
    } catch (PDOException $e) {
        handleDbIssuePdoXml($db);

        exit;
    }

    // display all artists
    $noresult = true;
    while ($track = $stmt->fetch()) {
        $noresult = false;
        $nb_artist_tracks = getNumberOfTracksForArtist($db, $track[0], true);
        if (checkIfResultAlreadyThere($w->results(), '👤 '.$track[0].' ('.$nb_artist_tracks.' tracks)') == false) {
            $uri = $track[2];
            // in case of local track, pass track uri instead
            if ($uri == '') {
                $uri = $track[3];
            }

            $w->result(null, '', '👤 '.$track[0].' ('.$nb_artist_tracks.' tracks)', 'Browse this artist', $track[1], 'no', null, 'Artist▹'.$uri.'∙'.$track[0].'∙'.' ★ '.'▹');
        }
    }

    if ($noresult) {
        $w->result(null, 'help', 'There is no result for your search', '', './images/warning.png', 'no', null, '');
    }
}

/**
 * secondDelimiterSettings function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterSettings($w, $query, $settings, $db, $update_in_progress)
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
    $output_application = $settings->output_application;

    $setting_kind = $words[1];
    $the_query = $words[2];

    if ($setting_kind == 'MaxResults') {
        if (mb_strlen($the_query) == 0) {
            $w->result(null, '', 'Enter the Max Results number (must be greater than 0):', 'Recommendation is between 10 to 100', './images/settings.png', 'no', null, '');
        } else {
            // max results has been set
            if (is_numeric($the_query) == true && $the_query > 0) {
                $w->result(null, serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            'MAX_RESULTS▹'.$the_query /* other_settings*/,
                            '' /* other_action */,
                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'Max Results will be set to <'.$the_query.'>', 'Type enter to validate the Max Results', './images/settings.png', 'yes', null, '');
            } else {
                $w->result(null, '', 'The Max Results value entered is not valid', 'Please fix it', './images/warning.png', 'no', null, '');
            }
        }
    } elseif ($setting_kind == 'Users') {

        $user_id = getCurrentUser($w);
        $w->result(null, '', 'Current user is ' . $user_id . '', 'Select one of the options below', './images/info.png', 'no', null, '');

        $users_folder = $w->data().'/users/';

        $users = scandir($users_folder);

        // loop on users
        foreach ($users as $user) {
            if ($user == '.' || $user == '..' || $user == $user_id || $user == '.DS_Store') {
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

        $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'SWITCH_USER▹NEW_USER' /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Add a new user', 'Type enter to validate', './images/artists.png', 'yes', null, '');

    } elseif ($setting_kind == 'RadioTracks') {
        if (mb_strlen($the_query) == 0) {
            $w->result(null, '', 'Enter the number of tracks to get when creating a radio Playlist:', 'Must be between 1 and 100', './images/settings.png', 'no', null, '');
        } else {
            // number radio tracks has been set
            if (is_numeric($the_query) == true && $the_query > 0 && $the_query <= 100) {
                $w->result(null, serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            'RADIO_TRACKS▹'.$the_query /* other_settings*/,
                            '' /* other_action */,
                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '' /* playlist_artwork_path */,
                            '', /* $alfred_playlist_name */
                        )), 'Number of Radio Tracks will be set to <'.$the_query.'>', 'Type enter to validate the Radio Tracks number', './images/settings.png', 'yes', null, '');
            } else {
                $w->result(null, '', 'The number of tracks value entered is not valid', 'Please fix it, it must be a number between 1 and 100', './images/warning.png', 'no', null, '');
            }
        }
    } elseif ($setting_kind == 'VolumePercentage') {
        if (mb_strlen($the_query) == 0) {
            $w->result(null, '', 'Enter the percentage of volume:', 'Must be between 1 and 50', './images/settings.png', 'no', null, '');
        } else {
            // volume percent
            if (is_numeric($the_query) == true && $the_query > 0 && $the_query <= 50) {
                $w->result(null, serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            'VOLUME_PERCENT▹'.$the_query /* other_settings*/,
                            '' /* other_action */,
                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '' /* playlist_artwork_path */,
                            '', /* $alfred_playlist_name */
                        )), 'Volume Percentage will be set to <'.$the_query.'>', 'Type enter to validate the Volume Percentage number', './images/settings.png', 'yes', null, '');
            } else {
                $w->result(null, '', 'The number of volume percentage entered is not valid', 'Please fix it, it must be a number between 1 and 50', './images/warning.png', 'no', null, '');
            }
        }
    } elseif ($setting_kind == 'Output') {
        if ($output_application != 'APPLESCRIPT') {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'enable_applescript' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Use Spotify Desktop', array(
                    'You will use Spotify Desktop application with AppleScript',
                    'alt' => 'Not Available',
                    'cmd' => 'Not Available',
                    'shift' => 'Not Available',
                    'fn' => 'Not Available',
                    'ctrl' => 'Not Available',
                ), './images/spotify.png', 'yes', null, '');
        }

        if (isUserPremiumSubscriber($w)) {
            // only propose if user is premimum
            if ($output_application != 'CONNECT') {
                $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'enable_connect' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Use Spotify Connect', array(
                'You will use Spotify Connect to control your devices',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
                ), './images/connect.png', 'yes', null, '');
            }

            if ($output_application != 'MOPIDY') {
                $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'enable_mopidy' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Use Mopidy', array(
                'You will use Mopidy',
                'alt' => 'Not Available',
                'cmd' => 'Not Available',
                'shift' => 'Not Available',
                'fn' => 'Not Available',
                'ctrl' => 'Not Available',
                ), './images/enable_mopidy.png', 'yes', null, '');
            }
        } else {
            $w->result(null, 'help', 'Only premium users can use Mopidy and Spotify Connect', 'This is a Spotify limitation', './images/warning.png', 'no', null, '');
        }


    } elseif ($setting_kind == 'MopidyServer') {
        if (mb_strlen($the_query) == 0) {
            $w->result(null, '', 'Enter the server name or IP where Mopidy server is running:', 'Example: 192.168.0.5 or myserver.mydomain.mydomainextension', './images/settings.png', 'no', null, '');
        } else {
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'MOPIDY_SERVER▹'.$the_query /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '' /* playlist_artwork_path */,
                        '', /* $alfred_playlist_name */
                    )), 'Mopidy server will be set to <'.$the_query.'>', 'Type enter to validate the Mopidy server name or IP', './images/settings.png', 'yes', null, '');
        }
    } elseif ($setting_kind == 'MopidyPort') {
        if (mb_strlen($the_query) == 0) {
            $w->result(null, '', 'Enter the TCP port number where Mopidy server is running:', 'Must be a numeric value', './images/settings.png', 'no', null, '');
        } else {
            // tcp port has been set
            if (is_numeric($the_query) == true) {
                $w->result(null, serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            'MOPIDY_PORT▹'.$the_query /* other_settings*/,
                            '' /* other_action */,
                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '' /* playlist_artwork_path */,
                            '', /* $alfred_playlist_name */
                        )), 'Mopidy TCP port will be set to <'.$the_query.'>', 'Type enter to validate the Mopidy TCP port number', './images/settings.png', 'yes', null, '');
            } else {
                $w->result(null, '', 'The TCP port value entered is not valid', 'Please fix it, it must be a numeric value', './images/warning.png', 'no', null, '');
            }
        }
    }
}

/**
 * secondDelimiterFeaturedPlaylist function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterFeaturedPlaylist($w, $query, $settings, $db, $update_in_progress)
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

    $country = $words[1];

    if ($country == 'Choose a Country') {
        // list taken from http://charts.spotify.com/docs
        $spotify_country_codes = array(
            'ar',
            'at',
            'au',
            'be',
            'bg',
            'ch',
            'cl',
            'co',
            'cr',
            'cz',
            'de',
            'dk',
            'ec',
            'ee',
            'es',
            'fi',
            'fr',
            'gb',
            'gr',
            'gt',
            'hk',
            'hu',
            'ie',
            'is',
            'it',
            'li',
            'lt',
            'lu',
            'lv',
            'mx',
            'my',
            'nl',
            'no',
            'nz',
            'pe',
            'pl',
            'pt',
            'se',
            'sg',
            'sk',
            'sv',
            'tr',
            'tw',
            'us',
            'uy',
        );
        foreach ($spotify_country_codes as $spotify_country_code) {
            if (strtoupper($spotify_country_code) != 'US' && strtoupper($spotify_country_code) != 'GB' && strtoupper($spotify_country_code) != strtoupper($country_code)) {
                $w->result(null, '', getCountryName(strtoupper($spotify_country_code)), 'Browse the current featured playlists in '.getCountryName(strtoupper($spotify_country_code)), './images/star.png', 'no', null, 'Featured Playlist▹'.strtoupper($spotify_country_code).'▹');
            }
        }
    } else {
        try {
            $api = getSpotifyWebAPI($w);
            $featuredPlaylists = $api->getFeaturedPlaylists(array(
                    'country' => $country,
                    'limit' => ($max_results <= 50) ? $max_results : 50,
                    'offset' => 0,
                ));

            $subtitle = 'Launch Playlist';
            $playlists = $featuredPlaylists->playlists;
            $w->result(null, '', $featuredPlaylists->message, ''.$playlists->total.' playlists available', './images/info.png', 'no', null, '');
            $items = $playlists->items;
            foreach ($items as $playlist) {
                $w->result(null, '', '🎵'.escapeQuery($playlist->name), 'by '.$playlist->owner->id.' ● '.$playlist->tracks->total.' tracks', getPlaylistArtwork($w, $playlist->uri, false, false, $use_artworks), 'no', null, 'Online Playlist▹'.$playlist->uri.'∙'.escapeQuery($playlist->name).'▹');
            }
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            $w->result(null, 'help', 'Exception occurred', ''.$e->getMessage(), './images/warning.png', 'no', null, '');
            echo $w->tojson();
            exit;
        }
    }
}

/**
 * secondDelimiterNewReleases function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterNewReleases($w, $query, $settings, $db, $update_in_progress)
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

    $country = $words[1];

    if ($country == 'Choose a Country') {
        // list taken from http://charts.spotify.com/docs
        $spotify_country_codes = array(
            'ar',
            'at',
            'au',
            'be',
            'bg',
            'ch',
            'cl',
            'co',
            'cr',
            'cz',
            'de',
            'dk',
            'ec',
            'ee',
            'es',
            'fi',
            'fr',
            'gb',
            'gr',
            'gt',
            'hk',
            'hu',
            'ie',
            'is',
            'it',
            'li',
            'lt',
            'lu',
            'lv',
            'mx',
            'my',
            'nl',
            'no',
            'nz',
            'pe',
            'pl',
            'pt',
            'se',
            'sg',
            'sk',
            'sv',
            'tr',
            'tw',
            'us',
            'uy',
        );
        foreach ($spotify_country_codes as $spotify_country_code) {
            if (strtoupper($spotify_country_code) != 'US' && strtoupper($spotify_country_code) != 'GB' && strtoupper($spotify_country_code) != strtoupper($country_code)) {
                $w->result(null, '', getCountryName(strtoupper($spotify_country_code)), 'Browse the new album releases in '.getCountryName(strtoupper($spotify_country_code)), './images/new_releases.png', 'no', null, 'New Releases▹'.strtoupper($spotify_country_code).'▹');
            }
        }
    } else {
        if (substr_count($query, '@') == 0) {

            // Get New Releases Online

            // call to web api, if it fails,
            // it displays an error in main window
            $albums = getTheNewReleases($w, $country, $max_results);

            $w->result(null, 'help', 'Select an album below to browse it', 'singles and compilations are also displayed', './images/info.png', 'no', null, '');

            $noresult = true;
            foreach ($albums as $album) {
                if (checkIfResultAlreadyThere($w->results(), $album->name.' ('.count($album->tracks->items).' tracks)') == false) {
                    $noresult = false;
                    $genre = (count($album->genres) > 0) ? ' ● Genre: '.implode('|', $album->genres) : '';
                    $tracks = $album->tracks;
                    $w->result(null, '', $album->name.' ('.count($album->tracks->items).' tracks)', $album->album_type.' by '.$album->artists[0]->name.' ● Release date: '.$album->release_date.$genre, getTrackOrAlbumArtwork($w, $album->uri, false, false, false, $use_artworks), 'no', null, 'New Releases▹'.$country.'▹'.$album->uri.'@'.$album->name);
                }
            }

            if ($noresult) {
                $w->result(null, 'help', 'There is no album for this artist', '', './images/warning.png', 'no', null, '');
            }
        } elseif (substr_count($query, '@') == 1) {

            // Search Album Online

            $tmp = $words[2];
            $words = explode('@', $tmp);
            $album_uri = $words[0];
            $album_name = $words[1];

            $album_artwork_path = getTrackOrAlbumArtwork($w, $album_uri, false, false, false, $use_artworks);
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        $album_uri /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'playalbum' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        $album_name /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        $album_artwork_path /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), '💿 '.escapeQuery($album_name), 'Play album', $album_artwork_path, 'yes', null, '');

            if ($update_in_progress == false) {
                $w->result(null, '', 'Add album '.escapeQuery($album_name).' to...', 'This will add the album to Your Music or a playlist you will choose in next step', './images/add.png', 'no', null, 'Add▹'.$album_uri.'∙'.escapeQuery($album_name).'▹');
            }

            // call to web api, if it fails,
            // it displays an error in main window
            $tracks = getTheAlbumFullTracks($w, $album_uri);

            $noresult = true;
            foreach ($tracks as $track) {
                // if ($noresult == true) {
                //     $subtitle = "⌥ (play album) ⌘ (play artist) ctrl (lookup online)";
                //     $subtitle = "$subtitle fn (add track to ...) ⇧ (add album to ...)";
                //     $w->result(null, 'help', "Select a track below to play it (or choose alternative described below)", $subtitle, './images/info.png', 'no', null, '');
                // }
                // $noresult           = false;
                $track_artwork_path = getTrackOrAlbumArtwork($w, $track->uri, false, false, false, $use_artworks);
                $w->result(null, serialize(array(
                            $track->uri /*track_uri*/,
                            $album_uri /* album_uri */,
                            $track->artists[0]->uri /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            '' /* other_settings*/,
                            'play_track_in_album_context' /* other_action */,
                            $track->artists[0]->name /* artist_name */,
                            $track->name /* track_name */,
                            $album_name /* album_name */,
                            $track_artwork_path /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), escapeQuery($track->artists[0]->name).' ● '.escapeQuery($track->name), array(
                        beautifyTime($track->duration_ms / 1000).' ● '.$album_name,
                        'alt' => 'Play album '.escapeQuery($album_name).' in Spotify',
                        'cmd' => 'Play artist '.escapeQuery($track->artists[0]->name).' in Spotify',
                        'fn' => 'Add track '.escapeQuery($track->name).' to ...',
                        'shift' => 'Add album '.escapeQuery($album_name).' to ...',
                        'ctrl' => 'Search artist '.escapeQuery($track->artists[0]->name).' online',
                    ), $track_artwork_path, 'yes', null, '');
            }
        }
    }
}

/**
 * secondDelimiterAdd function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterAdd($w, $query, $settings, $db, $update_in_progress)
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

    $is_public_playlists = $settings->is_public_playlists;

    if ($update_in_progress == true) {
        $w->result(null, '', 'Cannot add tracks/albums/playlists while update is in progress', 'Please retry when update is finished', './images/warning.png', 'no', null, '');

        echo $w->tojson();

        return;
    }

    $tmp = explode('∙', $words[1]);
    $uri = $tmp[0];

    $track_name = '';
    $track_uri = '';
    $album_name = '';
    $album_uri = '';
    $playlist_name = '';
    $playlist_uri = '';

    $href = explode(':', $uri);
    $message = '';
    $type = '';
    if ($href[1] == 'track') {
        $type = 'track';
        $track_name = $tmp[1];
        $track_uri = $uri;
        $message = 'track '.$track_name;
    } elseif ($href[1] == 'album') {
        $type = 'album';
        $album_name = $tmp[1];
        $album_uri = $uri;
        $message = 'album  '.$album_name;
    } elseif ($href[1] == 'user') {
        $type = 'playlist';
        $playlist_name = $tmp[1];
        $playlist_uri = $uri;
        $message = 'playlist '.$playlist_name;
    } elseif ($href[1] == 'local') {
        $w->result(null, '', 'Cannot add local track to playlist using the Web API', 'This is a limitation of Spotify Web API', './images/warning.png', 'no', null, '');
        echo $w->tojson();

        return;
    }
    $theplaylist = $words[2];

    try {
        if (mb_strlen($theplaylist) < 2) {
            $getPlaylists = 'select uri,name,nb_tracks,author,username,playlist_artwork_path,ownedbyuser,nb_playable_tracks,duration_playlist from playlists where ownedbyuser=1';
            $stmt = $db->prepare($getPlaylists);

            $w->result(null, '', 'Add '.$type.' '.$tmp[1].' to Your Music or one of your playlists below..', 'Select Your Music or one of your playlists below to add the '.$message, './images/add.png', 'no', null, '');

            $privacy_status = 'private';
            if ($is_public_playlists) {
                $privacy_status = 'public';
            }
            $w->result(null, '', 'Create a new playlist ', 'Create a new '.$privacy_status.' playlist and add the '.$message, './images/create_playlist.png', 'no', null, $query.'Enter Playlist Name▹');

            // put Alfred Playlist at beginning
            if ($is_alfred_playlist_active == true) {
                if ($alfred_playlist_uri != '' && $alfred_playlist_name != '') {
                    $w->result(null, serialize(array(
                                $track_uri /*track_uri*/,
                                $album_uri /* album_uri */,
                                '' /* artist_uri */,
                                $playlist_uri /* playlist_uri */,
                                '' /* spotify_command */,
                                '' /* query */,
                                'ADD_TO_PLAYLIST▹'.$alfred_playlist_uri.'▹'.$alfred_playlist_name /* other_settings*/,
                                '' /* other_action */,

                                '' /* artist_name */,
                                $track_name /* track_name */,
                                $album_name /* album_name */,
                                '' /* track_artwork_path */,
                                '' /* artist_artwork_path */,
                                '' /* album_artwork_path */,
                                $playlist_name /* playlist_name */,
                                '', /* playlist_artwork_path */
                            )), '🎵 Alfred Playlist '.' ● '.$alfred_playlist_name, 'Select the playlist to add the '.$message, './images/alfred_playlist.png', 'yes', null, '');
                }
            }

            $w->result(null, serialize(array(
                        $track_uri /*track_uri*/,
                        $album_uri /* album_uri */,
                        '' /* artist_uri */,
                        $playlist_uri /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'ADD_TO_YOUR_MUSIC▹' /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        $track_name /* track_name */,
                        $album_name /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        $playlist_name /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'Your Music', 'Select to add the '.$message.' to Your Music', './images/yourmusic.png', 'yes', null, '');
        } else {
            $getPlaylists = 'select uri,name,nb_tracks,author,username,playlist_artwork_path,ownedbyuser,nb_playable_tracks,duration_playlist from playlists where ownedbyuser=1 and ( name like :playlist or author like :playlist)';
            $stmt = $db->prepare($getPlaylists);
            $stmt->bindValue(':playlist', '%'.$theplaylist.'%');
        }

        $playlists = $stmt->execute();
    } catch (PDOException $e) {
        handleDbIssuePdoXml($db);

        exit;
    }

    while ($playlist = $stmt->fetch()) {
        if (($playlist[0] != $alfred_playlist_uri && (mb_strlen($theplaylist) < 2)) || (mb_strlen($theplaylist) >= 3)) {
            $added = ' ';
            if (startswith($playlist[1], 'Artist radio for')) {
                $added = '📻 ';
            }
            $w->result(null, serialize(array(
                        $track_uri /*track_uri*/,
                        $album_uri /* album_uri */,
                        '' /* artist_uri */,
                        $playlist_uri /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'ADD_TO_PLAYLIST▹'.$playlist[0].'▹'.$playlist[1] /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        $track_name /* track_name */,
                        $album_name /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        $playlist_name /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), '🎵'.$added.$playlist[1], $playlist[7].' tracks ● '.$playlist[8].' ● Select the playlist to add the '.$message, $playlist[5], 'yes', null, '');
        }
    }
}

/**
 * secondDelimiterRemove function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterRemove($w, $query, $settings, $db, $update_in_progress)
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

    if ($update_in_progress == true) {
        $w->result(null, '', 'Cannot remove tracks while update is in progress', 'Please retry when update is finished', './images/warning.png', 'no', null, '');

        echo $w->tojson();

        return;
    }

    $tmp = explode('∙', $words[1]);
    $uri = $tmp[0];
    $href = explode(':', $uri);
    // it is necessarly a track:
    $type = 'track';
    $track_name = $tmp[1];
    $track_uri = $uri;
    $message = 'track '.$track_name;
    $theplaylist = $words[2];

    if ($href[1] == 'local') {
        $w->result(null, '', 'Cannot remove local tracks from playlists using the Web API', 'This is a limitation of Spotify Web API', './images/warning.png', 'no', null, '');
        echo $w->tojson();

        return;
    }

    $noresult = true;
    $getPlaylistsForTrack = 'select distinct playlist_uri from tracks where uri=:uri';
    try {
        $stmt = $db->prepare($getPlaylistsForTrack);
        $stmt->bindValue(':uri', ''.$track_uri.'');
        $stmt->execute();

        while ($playlistsForTrack = $stmt->fetch()) {
            if ($playlistsForTrack[0] == '') {
                if ($noresult == true) {
                    $w->result(null, '', 'Remove '.$type.' '.$tmp[1].' from Your Music or one of your playlists below..', 'Select Your Music or one of your playlists below to remove the '.$message, './images/add.png', 'no', null, '');
                }
                // Your Music
                $w->result(null, serialize(array(
                            $track_uri /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            'REMOVE_FROM_YOUR_MUSIC▹' /* other_settings*/,
                            '' /* other_action */,
                            '' /* artist_name */,
                            $track_name /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'Your Music', 'Select to remove the '.$message.' from Your Music', './images/yourmusic.png', 'yes', null, '');
                $noresult = false;
            } else {
                if (mb_strlen($theplaylist) < 2) {
                    $getPlaylists = 'select uri,name,nb_tracks,author,username,playlist_artwork_path,ownedbyuser,nb_playable_tracks,duration_playlist from playlists where ownedbyuser=1 and uri=:playlist_uri';
                    $stmtGetPlaylists = $db->prepare($getPlaylists);
                    $stmtGetPlaylists->bindValue(':playlist_uri', $playlistsForTrack[0]);
                } else {
                    $getPlaylists = 'select uri,name,nb_tracks,author,username,playlist_artwork_path,ownedbyuser,nb_playable_tracks,duration_playlist from playlists where ownedbyuser=1 and ( name like :playlist or author like :playlist) and uri=:playlist_uri';
                    $stmtGetPlaylists = $db->prepare($getPlaylists);
                    $stmtGetPlaylists->bindValue(':playlist_uri', $playlistsForTrack[0]);
                    $stmtGetPlaylists->bindValue(':playlist', '%'.$theplaylist.'%');
                }

                $playlists = $stmtGetPlaylists->execute();

                while ($playlist = $stmtGetPlaylists->fetch()) {
                    if ($noresult == true) {
                        $w->result(null, '', 'Remove '.$type.' '.$tmp[1].' from Your Music or one of your playlists below..', 'Select Your Music or one of your playlists below to remove the '.$message, './images/add.png', 'no', null, '');
                    }
                    $added = ' ';
                    if (startswith($playlist[1], 'Artist radio for')) {
                        $added = '📻 ';
                    }
                    $w->result(null, serialize(array(
                                $track_uri /*track_uri*/,
                                '' /* album_uri */,
                                '' /* artist_uri */,
                                '' /* playlist_uri */,
                                '' /* spotify_command */,
                                '' /* query */,
                                'REMOVE_FROM_PLAYLIST▹'.$playlist[0].'▹'.$playlist[1] /* other_settings*/,
                                '' /* other_action */,
                                '' /* artist_name */,
                                $track_name /* track_name */,
                                '' /* album_name */,
                                '' /* track_artwork_path */,
                                '' /* artist_artwork_path */,
                                '' /* album_artwork_path */,
                                '' /* playlist_name */,
                                '', /* playlist_artwork_path */
                            )), '🎵'.$added.$playlist[1], $playlist[7].' tracks ● '.$playlist[8].' ● Select the playlist to remove the '.$message, $playlist[5], 'yes', null, '');
                    $noresult = false;
                }
            }
        }
    } catch (PDOException $e) {
        handleDbIssuePdoXml($db);

        exit;
    }

    if ($noresult) {
        $w->result(null, 'help', 'The current track is not in Your Music or one of your playlists', '', './images/warning.png', 'no', null, '');
    }
}

/**
 * secondDelimiterAlfredPlaylist function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterAlfredPlaylist($w, $query, $settings, $db, $update_in_progress)
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

    $setting_kind = $words[1];
    $theplaylist = $words[2];

    if ($setting_kind == 'Set Alfred Playlist') {
        $w->result(null, '', 'Set your Alfred playlist', 'Select one of your playlists below as your Alfred playlist', './images/settings.png', 'no', null, '');

        try {
            if (mb_strlen($theplaylist) < 2) {
                $getPlaylists = 'select uri,name,nb_tracks,author,username,playlist_artwork_path,ownedbyuser,nb_playable_tracks,duration_playlist from playlists where ownedbyuser=1';
                $stmt = $db->prepare($getPlaylists);
            } else {
                $getPlaylists = 'select uri,name,nb_tracks,author,username,playlist_artwork_path,ownedbyuser,nb_playable_tracks,duration_playlist from playlists where ownedbyuser=1 and ( name like :playlist or author like :playlist)';
                $stmt = $db->prepare($getPlaylists);
                $stmt->bindValue(':playlist', '%'.$theplaylist.'%');
            }

            $playlists = $stmt->execute();
        } catch (PDOException $e) {
            handleDbIssuePdoXml($db);

            return;
        }

        while ($playlist = $stmt->fetch()) {
            $added = ' ';
            if (startswith($playlist[1], 'Artist radio for')) {
                $added = '📻 ';
            }
            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'ALFRED_PLAYLIST▹'.$playlist[0].'▹'.$playlist[1] /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), '🎵'.$added.$playlist[1], $playlist[7].' tracks ● '.$playlist[8].' ● Select the playlist to set it as your Alfred Playlist', $playlist[5], 'yes', null, '');
        }
    } elseif ($setting_kind == 'Confirm Clear Alfred Playlist') {
        $w->result(null, '', 'Are you sure?', 'This will remove all the tracks in your current Alfred Playlist.', './images/warning.png', 'no', null, '');

        $w->result(null, '', 'No, cancel', 'Return to Alfred Playlist', './images/uncheck.png', 'no', null, 'Alfred Playlist▹');

        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    'CLEAR_ALFRED_PLAYLIST▹'.$alfred_playlist_uri.'▹'.$alfred_playlist_name /* other_settings*/,
                    '' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Yes, go ahead', 'This is undoable', './images/check.png', 'yes', null, '');
    }
}

/**
 * secondDelimiterFollowUnfollow function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterFollowUnfollow($w, $query, $settings, $db, $update_in_progress)
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

    if (substr_count($query, '@') == 1) {

        // Follow / Unfollow artist Option menu

        $tmp = $words[1];
        $words = explode('@', $tmp);
        $artist_uri = $words[0];
        $tmp_uri = explode(':', $artist_uri);

        $artist_name = $words[1];

        try {
            $api = getSpotifyWebAPI($w);
            $isArtistFollowed = $api->currentUserFollows('artist', $tmp_uri[2]);

            $artist_artwork_path = getArtistArtwork($w, $artist_uri, $artist_name, false, false, false, $use_artworks);
            if (!$isArtistFollowed[0]) {
                $w->result(null, '', 'Follow artist '.$artist_name, 'You are not currently following the artist', $artist_artwork_path, 'no', null, 'Follow▹'.$artist_uri.'@'.$artist_name.'▹');
            } else {
                $w->result(null, '', 'Unfollow artist '.$artist_name, 'You are currently following the artist', $artist_artwork_path, 'no', null, 'Unfollow▹'.$artist_uri.'@'.$artist_name.'▹');
            }
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            $w->result(null, 'help', 'Exception occurred', ''.$e->getMessage(), './images/warning.png', 'no', null, '');
            echo $w->tojson();
            exit;
        }
    }
}

/**
 * secondDelimiterFollowOrUnfollow function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterFollowOrUnfollow($w, $query, $settings, $db, $update_in_progress)
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

    if (substr_count($query, '@') == 1) {

        // Follow / Unfollow actions

        $tmp = $words[1];
        $words = explode('@', $tmp);
        $artist_uri = $words[0];
        $tmp_uri = explode(':', $artist_uri);

        $artist_name = $words[1];

        if ($kind == 'Follow') {
            $follow = true;
        } else {
            $follow = false;
        }
        try {
            $api = getSpotifyWebAPI($w);
            if ($follow) {
                $ret = $api->followArtistsOrUsers('artist', $tmp_uri[2]);
            } else {
                $ret = $api->unfollowArtistsOrUsers('artist', $tmp_uri[2]);
            }

            if ($ret) {
                if ($follow) {
                    displayNotificationWithArtwork($w, 'You are now following the artist '.$artist_name, './images/follow.png', 'Follow');
                    exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Artist▹'.$artist_uri.'∙'.escapeQuery($artist_name).'▹'."\"'");
                } else {
                    displayNotificationWithArtwork($w, 'You are no more following the artist '.$artist_name, './images/follow.png', 'Unfollow');
                    exec("osascript -e 'tell application \"Alfred 3\" to search \"".getenv('c_spot_mini').' Artist▹'.$artist_uri.'∙'.escapeQuery($artist_name).'▹'."\"'");
                }
            } else {
                $w->result(null, '', 'Error!', 'An error happened, try again or report to the author', './images/warning.png', 'no', null, '');
            }
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            $w->result(null, 'help', 'Exception occurred', ''.$e->getMessage(), './images/warning.png', 'no', null, '');
            echo $w->tojson();
            exit;
        }
    }
}

/**
 * secondDelimiterDisplayBiography function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterDisplayBiography($w, $query, $settings, $db, $update_in_progress)
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

    if (substr_count($query, '∙') == 1) {

        // Search Biography

        $tmp = $words[1];
        $words = explode('∙', $tmp);
        $artist_uri = $words[0];
        $artist_name = $words[1];

        list($biography_url, $source, $biography, $twitter_url, $official_url) = getBiography($w, $artist_uri, $artist_name);

        if ($biography_url != false) {
            if ($source == 'Last FM') {
                $image = './images/lastfm.png';
            } elseif ($source == 'Wikipedia') {
                $image = './images/wikipedia.png';
            } else {
                $image = './images/biography.png';
            }

            if ($twitter_url != '') {
                $twitter_account = end((explode('/', rtrim($twitter_url, '/'))));
                $w->result(null, serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            'Open▹'.$twitter_url /* other_settings*/,
                            '' /* other_action */,

                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'See twitter account @'.$twitter_account, 'This will open your default browser with the twitter of the artist', './images/twitter.png', 'yes', null, '');
            }

            if ($official_url != '') {
                $w->result(null, serialize(array(
                            '' /*track_uri*/,
                            '' /* album_uri */,
                            '' /* artist_uri */,
                            '' /* playlist_uri */,
                            '' /* spotify_command */,
                            '' /* query */,
                            'Open▹'.$official_url /* other_settings*/,
                            '' /* other_action */,

                            '' /* artist_name */,
                            '' /* track_name */,
                            '' /* album_name */,
                            '' /* track_artwork_path */,
                            '' /* artist_artwork_path */,
                            '' /* album_artwork_path */,
                            '' /* playlist_name */,
                            '', /* playlist_artwork_path */
                        )), 'See official website for the artist ('.$official_url.')', 'This will open your default browser with the official website of the artist', './images/artists.png', 'yes', null, '');
            }

            $w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        'Open▹'.$biography_url /* other_settings*/,
                        '' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
                    )), 'See biography for '.$artist_name.' on '.$source, 'This will open your default browser', $image, 'yes', null, '');

            $wrapped = wordwrap($biography, 70, "\n", false);
            $biography_sentances = explode("\n", $wrapped);
            $artist_artwork_path = getArtistArtwork($w, $artist_uri, $artist_name, false, false, false, $use_artworks);
            for ($i = 0; $i < count($biography_sentances); ++$i) {
                $w->result(null, '', $biography_sentances[$i], '', $artist_artwork_path, 'no', null, '');
            }
        } else {
            $w->result(null, 'help', 'No biography found!', '', './images/warning.png', 'no', null, '');
            echo $w->tojson();
            exit;
        }
    }
}

/**
 * secondDelimiterDisplayConfirmRemovePlaylist function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterDisplayConfirmRemovePlaylist($w, $query, $settings, $db, $update_in_progress)
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

    if (substr_count($query, '∙') == 1) {
        $tmp = $words[1];
        $words = explode('∙', $tmp);
        $playlist_uri = $words[0];
        $playlist_name = $words[1];
        $w->result(null, '', 'Are you sure?', 'This will remove the playlist from your library.', './images/warning.png', 'no', null, '');

        $w->result(null, '', 'No, cancel', 'Return to the playlist menu', './images/uncheck.png', 'no', null, 'Playlist▹'.$playlist_uri.'▹');

        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    $playlist_uri /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'unfollow_playlist' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    $playlist_name /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Yes, go ahead', 'You can always recover a removed playlist by choosing option below', './images/check.png', 'yes', null, '');

        $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    'Open▹'.'https://www.spotify.com/us/account/recover-playlists/' /* other_settings*/,
                    '' /* other_action */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '', /* playlist_artwork_path */
                )), 'Open Spotify web page to recover any of your playlists', 'This will open the Spotify page with your default browser', './images/spotify.png', 'yes', null, '');
    }
}

/**
 * secondDelimiterBrowse function.
 *
 * @param mixed $w
 * @param mixed $query
 * @param mixed $settings
 * @param mixed $db
 * @param mixed $update_in_progress
 */
function secondDelimiterBrowse($w, $query, $settings, $db, $update_in_progress)
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

    $country = $words[1];

    if ($country == 'Choose a Country') {
        // list taken from http://charts.spotify.com/docs
        $spotify_country_codes = array(
            'ar',
            'at',
            'au',
            'be',
            'bg',
            'ch',
            'cl',
            'co',
            'cr',
            'cz',
            'de',
            'dk',
            'ec',
            'ee',
            'es',
            'fi',
            'fr',
            'gb',
            'gr',
            'gt',
            'hk',
            'hu',
            'ie',
            'is',
            'it',
            'li',
            'lt',
            'lu',
            'lv',
            'mx',
            'my',
            'nl',
            'no',
            'nz',
            'pe',
            'pl',
            'pt',
            'se',
            'sg',
            'sk',
            'sv',
            'tr',
            'tw',
            'us',
            'uy',
        );
        foreach ($spotify_country_codes as $spotify_country_code) {
            if (strtoupper($spotify_country_code) != 'US' && strtoupper($spotify_country_code) != 'GB' && strtoupper($spotify_country_code) != strtoupper($country_code)) {
                $w->result(null, '', getCountryName(strtoupper($spotify_country_code)), 'Browse the Spotify categories in '.getCountryName(strtoupper($spotify_country_code)), './images/browse.png', 'no', null, 'Browse▹'.strtoupper($spotify_country_code).'▹');
            }
        }
    } else {
        try {
            $api = getSpotifyWebAPI($w);
            $offsetListCategories = 0;
            $limitListCategories = 50;
            do {
                // refresh api
                $api = getSpotifyWebAPI($w, $api);
                $listCategories = $api->getCategoriesList(array(
                        'country' => $country,
                        'limit' => $limitListCategories,
                        'locale' => '',
                        'offset' => $offsetListCategories,
                    ));
                $offsetListCategories += $limitListCategories;
            } while ($offsetListCategories < $listCategories->categories->total);

            foreach ($listCategories->categories->items as $category) {
                $w->result(null, '', escapeQuery($category->name), 'Browse this category', getCategoryArtwork($w, $category->id, $category->icons[0]->url, true, false, $use_artworks), 'no', null, 'Browse▹'.$country.'▹'.$category->id.'▹');
            }
        } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
            $w->result(null, 'help', 'Exception occurred', ''.$e->getMessage(), './images/warning.png', 'no', null, '');
            echo $w->tojson();

            exit;
        }
    }
}
