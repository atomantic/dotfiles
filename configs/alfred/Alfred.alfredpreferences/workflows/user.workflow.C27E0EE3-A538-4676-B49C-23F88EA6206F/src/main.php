<?php

require './src/functions.php';
require './src/menu.php';
require './src/firstDelimiter.php';
require './src/secondDelimiter.php';
require './src/thirdDelimiter.php';
require_once './src/workflows.php';

// $begin_time = computeTime();
// Report all PHP errors
//error_reporting(E_ALL);
error_reporting(0);
$w = new Workflows('com.vdesabou.spotify.mini.player');

$query = escapeQuery($argv[1]);
// thanks to http://www.alfredforum.com/topic/1788-prevent-flash-of-no-result
$query = iconv('UTF-8-MAC', 'UTF-8', $query);

// check for library update in progress
$update_in_progress = false;
if (file_exists($w->data().'/update_library_in_progress')) {
    $in_progress_data = $w->read('update_library_in_progress');
    $update_library_in_progress_words = explode('▹', $in_progress_data);

    $elapsed_time = time() - $update_library_in_progress_words[3];
    $update_in_progress = true;
    if (!file_exists($w->data().'/library_old.db')) {
        if (startsWith($update_library_in_progress_words[0], 'Init')) {
            if ($elapsed_time < 1800) {
                $w->result(null, $w->data().'/update_library_in_progress', 'Initialization phase since '.beautifyTime($elapsed_time, true).' : '.floatToSquares(0), 'Waiting for Spotify servers to return required data, it may take time depending on your library', './images/update_in_progress.png', 'no', null, '');
            } else {
                $w->result(null, '', 'There is a problem, the initialization phase took more than 30 minutes', 'Choose kill update library below, and report to the author', './images/warning.png', 'no', null, '');
                $w->result(null, serialize(array(
                    '' /*track_uri*/,
                    '' /* album_uri */,
                    '' /* artist_uri */,
                    '' /* playlist_uri */,
                    '' /* spotify_command */,
                    '' /* query */,
                    '' /* other_settings*/,
                    'kill_update' /* other_action */,
                    $alfred_playlist_uri /* alfred_playlist_uri */,
                    '' /* artist_name */,
                    '' /* track_name */,
                    '' /* album_name */,
                    '' /* track_artwork_path */,
                    '' /* artist_artwork_path */,
                    '' /* album_artwork_path */,
                    '' /* playlist_name */,
                    '' /* playlist_artwork_path */,
                    $alfred_playlist_name /* $alfred_playlist_name */,
                    $now_playing_notifications /* now_playing_notifications */,
                    $is_alfred_playlist_active /* is_alfred_playlist_active */,
                    $country_code /* country_code*/,
                    $userid,
                    /* userid*/
                )), 'Kill update library', 'This will stop the library update', './images/kill.png', 'yes', '');
            }
        } else {
            if ($update_library_in_progress_words[0] == 'Refresh Library') {
                $type = 'playlists';
            } elseif ($update_library_in_progress_words[0] == 'Artists') {
                $type = 'artists';
            } else {
                $type = 'tracks';
            }

            if ($update_library_in_progress_words[2] != 0) {
                $w->result(null, $w->data().'/update_library_in_progress', $update_library_in_progress_words[0].' in progress since '.beautifyTime($elapsed_time, true).' : '.floatToSquares(intval($update_library_in_progress_words[1]) / intval($update_library_in_progress_words[2])), $update_library_in_progress_words[1].'/'.$update_library_in_progress_words[2].' '.$type.' processed so far. Currently processing <'.$update_library_in_progress_words[4].'>', './images/update_in_progress.png', 'no', null, '');
            } else {
                $w->result(null, $w->data().'/update_library_in_progress', $update_library_in_progress_words[0].' in progress since '.beautifyTime($elapsed_time, true).' : '.floatToSquares(0), 'No '.$type.' processed so far', './images/update_in_progress.png', 'no', null, '');
            }
        }
        echo $w->tojson();

        return;
    }
}

// check for download artworks in progress
$download_artworks_in_progress = false;
if (file_exists($w->data().'/download_artworks_in_progress')) {
    $in_progress_data = $w->read('download_artworks_in_progress');
    $download_artworks_in_progress_words = explode('▹', $in_progress_data);
    $elapsed_time = time() - $download_artworks_in_progress_words[3];
    $download_artworks_in_progress = true;
    if ($download_artworks_in_progress_words[2] != 0) {
        $w->result(null, $w->data().'/download_artworks_in_progress', $download_artworks_in_progress_words[0].' in progress since '.beautifyTime($elapsed_time, true).' : '.floatToSquares(intval($download_artworks_in_progress_words[1]) / intval($download_artworks_in_progress_words[2])), $download_artworks_in_progress_words[1].'/'.$download_artworks_in_progress_words[2].' artworks processed so far (empty artworks can be seen until full download is complete)', './images/artworks.png', 'no', null, '');
    } else {
        $w->result(null, $w->data().'/download_artworks_in_progress', $download_artworks_in_progress_words[0].' in progress since '.beautifyTime($elapsed_time, true).' : '.floatToSquares(0), 'No artwork processed so far (empty artworks can be seen until full download is complete)', './images/artworks.png', 'no', null, '');
    }
}

// check for download artworks in progress
$change_theme_color_in_progress = false;
if (file_exists($w->data().'/change_theme_color_in_progress')) {
    $in_progress_data = $w->read('change_theme_color_in_progress');
    $change_theme_color_in_progress_words = explode('▹', $in_progress_data);
    $elapsed_time = time() - $change_theme_color_in_progress_words[3];
    $change_theme_color_in_progress = true;
    if ($change_theme_color_in_progress_words[2] != 0) {
        $w->result(null, $w->data().'/change_theme_color_in_progress', $change_theme_color_in_progress_words[0].' in progress since '.beautifyTime($elapsed_time, true).' : '.floatToSquares(intval($change_theme_color_in_progress_words[1]) / intval($change_theme_color_in_progress_words[2])), $change_theme_color_in_progress_words[1].'/'.$change_theme_color_in_progress_words[2].' icons processed so far (old icons can be seen until full download is complete)', './images/update_in_progress.png', 'no', null, '');
    } else {
        $w->result(null, $w->data().'/change_theme_color_in_progress', $change_theme_color_in_progress_words[0].' in progress since '.beautifyTime($elapsed_time, true).' : '.floatToSquares(0), 'No icons processed so far (old icons can be seen until full download is complete)', './images/update_in_progress.png', 'no', null, '');
    }
}

// Read settings from JSON

$settings = getSettings($w);
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

// Check that user is logged
oAuthChecks($w, $query, $settings, $update_in_progress);

// Check for library DB to use
$dbfile = '';
if ($update_in_progress == false && file_exists($w->data().'/library.db')) {
    $dbfile = $w->data().'/library.db';
} elseif (file_exists($w->data().'/library_old.db')) {
    // update in progress use the old library
    if ($update_in_progress == true) {
        $dbfile = $w->data().'/library_old.db';
    } else {
        unlink($w->data().'/library_old.db');
    }
}
if ($dbfile == '') {
    $w->result(null, serialize(array(
        '' /*track_uri*/,
        '' /* album_uri */,
        '' /* artist_uri */,
        '' /* playlist_uri */,
        '' /* spotify_command */,
        '' /* query */,
        '' /* other_settings*/,
        'update_library' /* other_action */,
        $alfred_playlist_uri /* alfred_playlist_uri */,
        '' /* artist_name */,
        '' /* track_name */,
        '' /* album_name */,
        '' /* track_artwork_path */,
        '' /* artist_artwork_path */,
        '' /* album_artwork_path */,
        '' /* playlist_name */,
        '' /* playlist_artwork_path */,
        $alfred_playlist_name /* $alfred_playlist_name */,
        $now_playing_notifications /* now_playing_notifications */,
        $is_alfred_playlist_active /* is_alfred_playlist_active */,
        $country_code /* country_code*/,
        $userid,
        /* userid*/
    )), 'Create library', "when done you'll receive a notification. you can check progress by invoking the workflow again", './images/update.png', 'yes', null, '');
    echo $w->tojson();

    return;
}
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
    handleDbIssuePdoXml($db);

    return;
}

// Check for workflow update
checkForUpdate($w, $last_check_update_time, false);

// thanks to http://www.alfredforum.com/topic/1788-prevent-flash-of-no-result
mb_internal_encoding('UTF-8');

// Fast access to commands

if (startsWith($query, ' ')) {
    searchCommandsFastAccess($w, ltrim($query), $settings, $db, $update_in_progress);
    echo $w->tojson();

    return;
}

// Specific case for hotkeys, in order to reset history
if (substr_count($query, '✧') == 1) {
    // empty history
    $w->write(array(), 'history.json');
    $query = ltrim($query, '✧');
}

if (mb_strlen($query) < 2) {
    // empty history
    $w->write(array(), 'history.json');

    ////////////

    // MAIN MENU

    ////////////
    mainMenu($w, $query, $settings, $db, $update_in_progress);
} else {
    // Go Back button appears only when typing 'bb'
    if (substr_count($query, 'bb') == 1) {
        $w->result(null, serialize(array(
            '' /*track_uri*/,
            '' /* album_uri */,
            '' /* artist_uri */,
            '' /* playlist_uri */,
            '' /* spotify_command */,
            '' /* query */,
            '' /* other_settings*/,
            'go_back' /* other_action */,
            '' /* alfred_playlist_uri */,
            '' /* artist_name */,
            '' /* track_name */,
            '' /* album_name */,
            '' /* track_artwork_path */,
            '' /* artist_artwork_path */,
            '' /* album_artwork_path */,
            '' /* playlist_name */,
            '' /* playlist_artwork_path */,
            '' /* $alfred_playlist_name */,
            '' /* now_playing_notifications */,
            '' /* is_alfred_playlist_active */,
            '' /* country_code*/,
            '',
            /* userid*/
        )), 'Go Back', 'Return to previous step', './images/back.png', 'yes', null, '');
    }

    ////////////

    // NO DELIMITER

    ////////////
    if (substr_count($query, '▹') == 0) {
        searchCategoriesFastAccess($w, $query, $settings, $db, $update_in_progress);
        searchCommandsFastAccess($w, $query, $settings, $db, $update_in_progress);
        mainSearch($w, $query, $settings, $db, $update_in_progress);
    } else {

        // Handle History
        $history = $w->read('history.json');
        if ($history == false) {
            $history = array();
        }
        array_push($history, substr($query, 0, strrpos($query, '▹')).'▹');
        $w->write(array_unique($history), 'history.json');

        ////////////

        // FIRST DELIMITER

        ////////////
        if (substr_count($query, '▹') == 1) {
            $words = explode('▹', $query);
            $kind = $words[0];
            if ($kind == 'Playlist') {
                firstDelimiterPlaylists($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Alfred Playlist') {
                firstDelimiterAlfredPlaylist($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Artist') {
                firstDelimiterArtists($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Album') {
                firstDelimiterAlbums($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Featured Playlist') {
                firstDelimiterFeaturedPlaylist($w, $query, $settings, $db, $update_in_progress);
            } elseif (startswith($kind, 'Search')) {
                firstDelimiterSearchOnline($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'New Releases') {
                firstDelimiterNewReleases($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Current Track') {
                firstDelimiterCurrentTrack($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Spotify Connect') {
                firstDelimiterSpotifyConnect($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Your Music') {
                firstDelimiterYourMusic($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Lyrics') {
                firstDelimiterLyrics($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Settings') {
                firstDelimiterSettings($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Check for update...') {
                firstDelimiterCheckForUpdate($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Play Queue') {
                firstDelimiterPlayQueue($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Browse') {
                firstDelimiterBrowse($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Your Tops') {
                firstDelimiterYourTops($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Recent Tracks') {
                firstDelimiterYourRecentTracks($w, $query, $settings, $db, $update_in_progress);
            }
        }
        ////////////

        // SECOND DELIMITER

        ////////////
        elseif (substr_count($query, '▹') == 2) {
            $words = explode('▹', $query);
            $kind = $words[0];
            if ($kind == 'Artist') {
                secondDelimiterArtists($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Album') {
                secondDelimiterAlbums($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Playlist') {
                secondDelimiterPlaylists($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Online') {
                secondDelimiterOnline($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'OnlineRelated') {
                secondDelimiterOnlineRelated($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Online Playlist') {
                secondDelimiterOnlinePlaylist($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Your Music' && $words[1] == 'Tracks') {
                secondDelimiterYourMusicTracks($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Your Music' && $words[1] == 'Albums') {
                secondDelimiterYourMusicAlbums($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Your Music' && $words[1] == 'Artists') {
                secondDelimiterYourMusicArtists($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Settings') {
                secondDelimiterSettings($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Featured Playlist') {
                secondDelimiterFeaturedPlaylist($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'New Releases') {
                secondDelimiterNewReleases($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Add') {
                secondDelimiterAdd($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Remove') {
                secondDelimiterRemove($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Alfred Playlist') {
                secondDelimiterAlfredPlaylist($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Follow/Unfollow') {
                secondDelimiterFollowUnfollow($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Follow' || $kind == 'Unfollow') {
                secondDelimiterFollowOrUnfollow($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Biography') {
                secondDelimiterDisplayBiography($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Confirm Remove Playlist') {
                secondDelimiterDisplayConfirmRemovePlaylist($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Browse') {
                secondDelimiterBrowse($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Your Tops' && $words[1] == 'Artists') {
                secondDelimiterYourTopArtists($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Your Tops' && $words[1] == 'Tracks') {
                secondDelimiterYourTopTracks($w, $query, $settings, $db, $update_in_progress);
            }
        }
        ///////////

        // THIRD DELIMITER

        ////////////
        elseif (substr_count($query, '▹') == 3) {
            $words = explode('▹', $query);
            $kind = $words[0];
            if ($kind == 'Add') {
                thirdDelimiterAdd($w, $query, $settings, $db, $update_in_progress);
            } elseif ($kind == 'Browse') {
                thirdDelimiterBrowse($w, $query, $settings, $db, $update_in_progress);
            }
        }
    }
}
/*
$end_time = computeTime();
$total_temp = ($end_time-$begin_time);
$w->result(null, 'debug', "Processed in " . $total_temp*1000 . ' ms', '', './images/info.png', 'no', null, '');
*/
echo $w->tojson();
