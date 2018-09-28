<?php

require './src/functions.php';
require_once './src/workflows.php';
$w = new Workflows('com.vdesabou.spotify.mini.player');

$query = $argv[1];

if (mb_strlen($query) > 1) {
    if (startsWith($query, 'DB Exception')) {
        $w->result(null, '', 'DB Exception occurred: '.$query, 'Try to re-create library as explained below.', './images/warning.png', 'no', null, '');
        $w->result(uniqid(), '', 'There is a problem with the library, try to re-create it.', 'Select Re-Create Library library below', './images/warning.png', 'no', null, '');
        $w->result(uniqid(), serialize(array(
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
    } elseif (startsWith($query, 'AppleScript Exception')) {
        $w->result(null, 'help', 'AppleScript execution failed!', 'Message: '.$query, './images/warning.png', 'no', null, '');
        $w->result(null, serialize(array(
            '' /*track_uri*/,
            '' /* album_uri */,
            '' /* artist_uri */,
            '' /* playlist_uri */,
            '' /* spotify_command */,
            '' /* query */,
            'Open▹'.'http://alfred-spotify-mini-player.com/blog/issue-with-latest-spotify-update/' /* other_settings*/,
            '' /* other_action */,
            '' /* artist_name */,
            '' /* track_name */,
            '' /* album_name */,
            '' /* track_artwork_path */,
            '' /* artist_artwork_path */,
            '' /* album_artwork_path */,
            '' /* playlist_name */,
            '', /* playlist_artwork_path */
        )), 'Maybe you have an issue with a Broken Spotify version?', 'Go to the article to get more information', './images/website.png', 'yes', null, '');
    } elseif (startsWith($query, 'Mopidy Exception')) {
        $w->result(null, 'help', 'Mopidy execution failed!', 'Message: '.$query, './images/warning.png', 'no', null, '');
        $w->result(null, serialize(array(
            '' /*track_uri*/,
            '' /* album_uri */,
            '' /* artist_uri */,
            '' /* playlist_uri */,
            '' /* spotify_command */,
            '' /* query */,
            'Open▹'.'http://alfred-spotify-mini-player.com/articles/mopidy/' /* other_settings*/,
            '' /* other_action */,
            '' /* artist_name */,
            '' /* track_name */,
            '' /* album_name */,
            '' /* track_artwork_path */,
            '' /* artist_artwork_path */,
            '' /* album_artwork_path */,
            '' /* playlist_name */,
            '', /* playlist_artwork_path */
        )), 'Is Mopidy correctly installed and running?', 'Go to the article to get more information', './images/website.png', 'yes', null, '');
    } else {
        $w->result(null, '', 'Exception occurred: '.$query, 'Use the Send an email to the author option below to send generated spot_mini_debug.zip', './images/warning.png', 'no', null, '');
    }
}

$w->result(null, serialize(array(
                        '' /*track_uri*/,
                        '' /* album_uri */,
                        '' /* artist_uri */,
                        '' /* playlist_uri */,
                        '' /* spotify_command */,
                        '' /* query */,
                        '' /* other_settings*/,
                        'spot_mini_debug' /* other_action */,
                        '' /* artist_name */,
                        '' /* track_name */,
                        '' /* album_name */,
                        '' /* track_artwork_path */,
                        '' /* artist_artwork_path */,
                        '' /* album_artwork_path */,
                        '' /* playlist_name */,
                        '', /* playlist_artwork_path */
)), 'Send an email to the author with a link to generated spot_mini_debug.zip file', 'This will open your default mail application, with all needed information for troubleshooting.', './images/mail.png', 'yes', null, '');

$w->result(null, '', 'Quick access to workflow folders:', '', './images/info.png', 'no', null, '');

$w->result(null, serialize(array(
    '' /*track_uri*/,
    '' /* album_uri */,
    '' /* artist_uri */,
    '' /* playlist_uri */,
    '' /* spotify_command */,
    '' /* query */,
    'Open▹'.$w->data() /* other_settings*/,
    '' /* other_action */,
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
    '', /* userid*/
)), 'Browse to App Support Folder', 'This will open the folder in Finder', 'fileicon:'.$w->data(), 'yes', null, '');

$w->result(null, serialize(array(
    '' /*track_uri*/,
    '' /* album_uri */,
    '' /* artist_uri */,
    '' /* playlist_uri */,
    '' /* spotify_command */,
    '' /* query */,
    'Open▹'.$w->cache() /* other_settings*/,
    '' /* other_action */,
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
    '', /* userid*/
)), 'Browse to Workflow Cache Folder', 'This will open the folder in Finder', 'fileicon:'.$w->cache(), 'yes', null, '');

$w->result(null, serialize(array(
    '' /*track_uri*/,
    '' /* album_uri */,
    '' /* artist_uri */,
    '' /* playlist_uri */,
    '' /* spotify_command */,
    '' /* query */,
    'Open▹'.exec('pwd') /* other_settings*/,
    '' /* other_action */,
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
    '', /* userid*/
)), 'Browse to Alfred workflow folder', 'This will open the folder in Finder', 'fileicon:'.exec('pwd'), 'yes', null, '');

$w->result(null, serialize(array(
    '' /*track_uri*/,
    '' /* album_uri */,
    '' /* artist_uri */,
    '' /* playlist_uri */,
    '' /* spotify_command */,
    '' /* query */,
    'Open▹'.$w->cache().'/action.log'/* other_settings*/,
    '' /* other_action */,
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
    '', /* userid*/
)), 'Open log file', 'This will open the log file', 'fileicon:'.$w->cache().'/action.log', 'yes', null, '');

$w->result(null, serialize(array(
    '' /*track_uri*/,
    '' /* album_uri */,
    '' /* artist_uri */,
    '' /* playlist_uri */,
    '' /* spotify_command */,
    '' /* query */,
    '' /* other_settings*/,
    'delete_artwork_folder' /* other_action */,
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
    '', /* userid*/
)), 'Delete artwork folder', 'This will erase all existing artworks and re-download them', './images/warning.png', 'yes', null, '');

echo $w->tojson();
